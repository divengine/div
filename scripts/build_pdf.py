import argparse
import json
import os
import re
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


def combine_markdown(files):
    parts = []
    for idx, path in enumerate(files):
        content = path.read_text(encoding="utf-8")
        if idx > 0:
            title = path.relative_to(DOCS_DIR).as_posix()
            parts.append(f"\n\n# {title}\n\n")
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


def sanitize_markdown_for_pdf(markdown: str) -> str:
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
    markdown = protect(r"^\\(?:newpage|tableofcontents)\s*$", markdown, latex_lines, "@@LATEXLINE")

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


def build_pdf(input_md, output_pdf):
    cmd = [
        "pandoc",
        str(input_md),
        "-o",
        str(output_pdf),
        "--pdf-engine=xelatex",
    ]
    subprocess.run(cmd, check=True)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--output", default="div-documentation.pdf")
    parser.add_argument("--no-mermaid", action="store_true")
    parser.add_argument("--order-file", default="docs/book-order.txt")
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

    title = "Div PHP Template Engine"
    subtitle = "Documentation"
    today = date.today().isoformat()
    cover_lines = [
        f"# {title}",
        f"## {subtitle}",
        f"Version: {version}" if version else "Version: unknown",
        f"Date: {today}",
        "",
        "\\newpage",
        "\\tableofcontents",
        "\\newpage",
        "",
    ]

    combined = "\n".join(cover_lines) + combine_markdown(files)
    combined = sanitize_markdown_for_pdf(combined)

    mermaid_cli = os.environ.get("MERMAID_CLI", "mmdc")
    puppeteer_config = ROOT / "scripts" / "puppeteer.json"
    if args.no_mermaid:
        rendered = combined
    else:
        rendered = render_mermaid(combined, mermaid_cli, puppeteer_config)

    book_md = BUILD_DIR / "book.md"
    book_md.write_text(rendered, encoding="utf-8", newline="\n")

    output_pdf = BUILD_DIR / args.output
    build_pdf(book_md, output_pdf)


if __name__ == "__main__":
    main()
