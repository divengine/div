import argparse
import html
import json
import os
import re
import shutil
import subprocess
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DOCS_DIR = ROOT / "docs"
BUILD_DIR = ROOT / "build"
MERMAID_DIR = BUILD_DIR / "mermaid"


def collect_markdown_files():
    files = sorted(DOCS_DIR.rglob("*.md"))
    readme = DOCS_DIR / "README.md"
    if readme in files:
        files.remove(readme)
        files.insert(0, readme)
    return files


def read_order_file(order_path: Path):
    files = []
    for raw_line in order_path.read_text(encoding="utf-8").splitlines():
        line = raw_line.strip()
        if not line or line.startswith("#"):
            continue

        path = (ROOT / line).resolve()

        if line.endswith("/") or path.is_dir():
            for md in sorted(path.rglob("*.md")):
                files.append(md)
            continue

        if path.is_file():
            files.append(path)

    return files


def slugify(text: str) -> str:
    slug = re.sub(r"[^a-z0-9]+", "-", text.lower()).strip("-")
    return slug or "section"


def build_wiki_link_map(files):
    link_map = {}
    anchors = {}
    for path in files:
        stem = path.stem
        anchor = slugify(stem)
        anchors[path] = anchor
        link_map[stem] = anchor
        link_map[f"{stem}.md"] = anchor
        rel = path.relative_to(DOCS_DIR).as_posix()
        link_map[rel] = anchor
        if rel.endswith(".md"):
            link_map[rel[:-3]] = anchor
    return link_map, anchors


def heading_shift_for_path(path: Path) -> int:
    stem = path.stem
    match = re.match(r"^(\d+(?:\.\d+)*)", stem)
    if not match:
        return 0
    depth = len(match.group(1).split("."))
    return max(0, depth - 1)


def shift_heading_levels(content: str, shift: int) -> str:
    if shift <= 0:
        return content

    lines = content.splitlines()
    out = []
    in_fence = False
    for line in lines:
        if line.lstrip().startswith("```"):
            in_fence = not in_fence
            out.append(line)
            continue

        if not in_fence:
            match = re.match(r"^(#{1,6})(\s+.*)$", line)
            if match:
                level = len(match.group(1))
                new_level = min(6, level + shift)
                out.append("#" * new_level + match.group(2))
                continue

        out.append(line)

    return "\n".join(out)


def format_section_number(parts: list[int]) -> str:
    if len(parts) == 1:
        return f"{parts[0]}."
    return ".".join(str(part) for part in parts)


def renumber_headings(content: str, base_nums: list[int]) -> str:
    if not base_nums:
        return content

    lines = content.splitlines()
    out = []
    in_fence = False
    base_level = None
    counters: list[int] = []

    for line in lines:
        if line.lstrip().startswith("```"):
            in_fence = not in_fence
            out.append(line)
            continue

        if in_fence:
            out.append(line)
            continue

        match = re.match(r"^(#{1,6})\s+(.*)$", line)
        if not match:
            out.append(line)
            continue

        level = len(match.group(1))
        title = match.group(2).strip()
        title = re.sub(r"^\d+(?:\.\d+)*\.?\s+", "", title)

        if base_level is None:
            base_level = level

        if level < base_level:
            base_level = level
            counters = []

        relative = level - base_level
        if relative <= 0:
            number_parts = base_nums
            counters = []
        else:
            idx = relative - 1
            if len(counters) <= idx:
                counters.extend([0] * (idx + 1 - len(counters)))
            counters[idx] += 1
            for reset_idx in range(idx + 1, len(counters)):
                counters[reset_idx] = 0
            number_parts = base_nums + counters[: relative]

        number_text = format_section_number(number_parts)
        out.append(f"{match.group(1)} {number_text} {title}")

    return "\n".join(out)


def combine_markdown(files, anchors):
    parts = []
    for idx, path in enumerate(files):
        content = path.read_text(encoding="utf-8")
        content = shift_heading_levels(content, heading_shift_for_path(path))
        match = re.match(r"^(\d+(?:\.\d+)*)", path.stem)
        base_nums = [int(part) for part in match.group(1).split(".")] if match else []
        content = renumber_headings(content, base_nums)
        anchor = anchors.get(path)
        if anchor:
            parts.append(f"\n\n\\hypertarget{{{anchor}}}{{}}\n\n")
        parts.append(content)
        parts.append("\n\n\\newpage\n\n")
    return "".join(parts).strip() + "\n"


def render_mermaid(markdown, mermaid_cli, puppeteer_config):
    pattern = re.compile(r"```mermaid\s*(.*?)```", re.S)
    MERMAID_DIR.mkdir(parents=True, exist_ok=True)
    counter = 0

    def repl(match):
        nonlocal counter
        counter += 1
        code = match.group(1).strip() + "\n"
        mmd_path = MERMAID_DIR / f"diagram_{counter}.mmd"
        png_path = MERMAID_DIR / f"diagram_{counter}.png"
        mmd_path.write_text(code, encoding="utf-8")

        cmd = [mermaid_cli, "-i", str(mmd_path), "-o", str(png_path)]
        if puppeteer_config:
            cmd.extend(["-p", str(puppeteer_config)])

        subprocess.run(cmd, check=True)
        return f"![]({png_path.as_posix()})"

    return pattern.sub(repl, markdown)


def sanitize_markdown_for_pdf(markdown: str, wiki_links: dict[str, str]) -> str:
    # Drop Obsidian-style embedded images and convert remote images to links.
    markdown = re.sub(r"!\[\[([^\]]+)\]\]", r"Image: \1", markdown)

    def replace_remote_image(match):
        alt = (match.group(1) or "").strip()
        url = match.group(2).strip()
        label = alt if alt else url
        return f"[{label}]({url})"

    markdown = re.sub(r"!\[([^\]]*)\]\((https?://[^)]+)\)", replace_remote_image, markdown)

    # Protect fenced code blocks, inline code, and raw LaTeX commands we inject.
    code_blocks = []
    inline_codes = []
    latex_lines = []

    def protect(pattern, text, bucket, token):
        def repl(match):
            bucket.append(match.group(0))
            return f"{token}{len(bucket) - 1}@@"

        return re.sub(pattern, repl, text, flags=re.S | re.M)

    markdown = protect(r"```.*?```", markdown, code_blocks, "@@CODEBLOCK")
    markdown = protect(r"`[^`]*`", markdown, inline_codes, "@@CODEINLINE")
    markdown = protect(
        r"^\\(?:newpage|tableofcontents|hypertarget\{[^}]+\}\{\}|thispagestyle\{[^}]+\}|begin\{center\}|end\{center\}|vspace\*?\{[^}]+\}|vfill|rule\{[^}]+\}\{[^}]+\}|Huge|LARGE|Large|large|normalsize|bfseries)\s*$",
        markdown,
        latex_lines,
        "@@LATEXLINE",
    )

    # Convert wiki-style links to internal anchors when possible.
    # Supports [[Page]] and [[Page|Label]].
    def replace_wiki_link(match):
        target = (match.group(1) or "").strip()
        label = (match.group(2) or "").strip()
        if not target:
            return ""
        anchor = wiki_links.get(target)
        display = label if label else target
        if anchor:
            return f"[{display}](#{anchor})"
        return display

    markdown = re.sub(r"\[\[([^\]|]+?)(?:\|([^\]]+?))?\]\]", replace_wiki_link, markdown)

    # Escape stray backslashes so LaTeX doesn't treat them as commands.
    markdown = markdown.replace("\\", "\\textbackslash{}")
    # Escape dollar signs to avoid Pandoc math parsing.
    markdown = markdown.replace("$", "\\$")

    def restore(text, bucket, token):
        for idx, original in enumerate(bucket):
            text = text.replace(f"{token}{idx}@@", original)
        return text

    markdown = restore(markdown, latex_lines, "@@LATEXLINE")
    markdown = restore(markdown, inline_codes, "@@CODEINLINE")
    markdown = restore(markdown, code_blocks, "@@CODEBLOCK")

    return markdown


def build_pdf(input_md, output_pdf, paper_size: str):
    cmd = [
        "pandoc",
        str(input_md),
        "-o",
        str(output_pdf),
        "--pdf-engine=xelatex",
        "-V",
        f"papersize={paper_size}",
        "-V",
        "geometry:left=0.7in,right=0.7in,top=0.9in,bottom=0.9in",
    ]
    subprocess.run(cmd, check=True)


def render_html_template(template_path: Path, output_path: Path, context: dict[str, str]) -> None:
    raw = template_path.read_text(encoding="utf-8")
    for key, value in context.items():
        raw = raw.replace(f"{{{{{key}}}}}", html.escape(value))
    output_path.write_text(raw, encoding="utf-8", newline="\n")


def render_html_to_pdf(html_path: Path, pdf_path: Path) -> None:
    try:
        from weasyprint import HTML  # type: ignore
    except Exception:
        HTML = None

    if HTML is not None:
        HTML(filename=str(html_path)).write_pdf(str(pdf_path))
        return

    wkhtmltopdf = shutil.which("wkhtmltopdf")
    if wkhtmltopdf:
        subprocess.run([wkhtmltopdf, "--quiet", str(html_path), str(pdf_path)], check=True)
        return

    raise SystemExit(
        "HTML-to-PDF renderer not found. Install weasyprint (pip install weasyprint) "
        "or wkhtmltopdf, or run without cover/back templates."
    )


def merge_pdfs(output_pdf: Path, parts: list[Path]) -> None:
    try:
        from pypdf import PdfReader, PdfWriter  # type: ignore
    except Exception:
        try:
            from PyPDF2 import PdfReader, PdfWriter  # type: ignore
        except Exception as exc:
            raise SystemExit(
                "PDF merger not available. Install pypdf (pip install pypdf) "
                "or remove cover/back templates."
            ) from exc

    writer = PdfWriter()
    for part in parts:
        if not part.exists():
            continue
        reader = PdfReader(str(part))
        for page in reader.pages:
            writer.add_page(page)
    with output_pdf.open("wb") as handle:
        writer.write(handle)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--output", default="div-documentation.pdf")
    parser.add_argument("--no-mermaid", action="store_true")
    parser.add_argument("--order-file", default="docs/book-order.txt")
    parser.add_argument(
        "--paper-size",
        default="letter",
        help="Paper size for PDF output (e.g., letter, a4).",
    )
    args = parser.parse_args()

    BUILD_DIR.mkdir(exist_ok=True)

    order_path = (ROOT / args.order_file).resolve()
    if order_path.is_file():
        files = read_order_file(order_path)
    else:
        files = collect_markdown_files()
    if not files:
        raise SystemExit("No markdown files found under docs/")

    version = ""
    composer = ROOT / "composer.json"
    if composer.is_file():
        data = json.loads(composer.read_text(encoding="utf-8"))
        version = (data.get("version") or "").strip()

    title = "Div"
    subtitle = "PHP Template Engine"
    author = "Rafa Rodríguez (@rafageist)"
    org = "Divengine Software Solutions (divengine.org)"
    today = date.today().isoformat()
    cover_lines = [
        "\\tableofcontents",
        "\\newpage",
        "",
    ]

    wiki_links, anchors = build_wiki_link_map(files)
    combined = "\n".join(cover_lines) + combine_markdown(files, anchors)
    combined = sanitize_markdown_for_pdf(combined, wiki_links)

    mermaid_cli = os.environ.get("MERMAID_CLI", "mmdc")
    if os.name == "nt":
        # Prefer the .cmd shim on Windows; the extensionless file may not be executable.
        if mermaid_cli.lower() in {"mmdc", "mmdc.exe"}:
            mermaid_cli = "mmdc.cmd"
        else:
            cli_path = Path(mermaid_cli)
            if cli_path.name.lower() == "mmdc" and cli_path.suffix == "":
                mermaid_cli = str(cli_path.with_suffix(".cmd"))

    resolved_cli = shutil.which(mermaid_cli)
    if resolved_cli:
        mermaid_cli = resolved_cli
    elif not args.no_mermaid:
        raise SystemExit(
            "Mermaid CLI not found. Install @mermaid-js/mermaid-cli or run with --no-mermaid."
        )
    puppeteer_config = ROOT / "scripts" / "puppeteer.json"
    if args.no_mermaid:
        rendered = combined
    else:
        rendered = render_mermaid(combined, mermaid_cli, puppeteer_config)

    book_md = BUILD_DIR / "book.md"
    book_md.write_text(rendered, encoding="utf-8", newline="\n")

    output_pdf = BUILD_DIR / args.output
    build_pdf(book_md, output_pdf, args.paper_size)

    cover_template = ROOT / "scripts" / "cover.html"
    back_template = ROOT / "scripts" / "back.html"
    use_cover = cover_template.is_file()
    use_back = back_template.is_file()
    if use_cover or use_back:
        paper_size = args.paper_size.strip()
        if paper_size.lower().startswith("a") and len(paper_size) <= 3:
            css_paper = paper_size.upper()
        else:
            css_paper = paper_size.lower()

        context = {
            "title": title,
            "subtitle": subtitle,
            "author": author,
            "org": org,
            "version": version if version else "unknown",
            "date": today,
            "page_size": css_paper,
        }
        parts = []

        if use_cover:
            cover_html = BUILD_DIR / "cover.html"
            cover_pdf = BUILD_DIR / "cover.pdf"
            render_html_template(cover_template, cover_html, context)
            render_html_to_pdf(cover_html, cover_pdf)
            parts.append(cover_pdf)

        parts.append(output_pdf)

        if use_back:
            back_html = BUILD_DIR / "back.html"
            back_pdf = BUILD_DIR / "back.pdf"
            render_html_template(back_template, back_html, context)
            render_html_to_pdf(back_html, back_pdf)
            parts.append(back_pdf)

        merged_pdf = BUILD_DIR / f"merged-{args.output}"
        merge_pdfs(merged_pdf, parts)
        merged_pdf.replace(output_pdf)


if __name__ == "__main__":
    main()
