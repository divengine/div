import argparse
import json
import re
import subprocess
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def run_git(args):
    result = subprocess.run(
        ["git", *args],
        cwd=ROOT,
        check=True,
        capture_output=True,
        text=True,
    )
    return result.stdout.strip()


def get_version():
    data = json.loads((ROOT / "composer.json").read_text(encoding="utf-8"))
    version = (data.get("version") or "").strip()
    if not version:
        raise SystemExit("composer.json is missing a version field.")
    return version


def get_last_tag():
    return run_git(["describe", "--tags", "--abbrev=0"])


def parse_version(value: str):
    value = value.strip()
    if value.startswith("v"):
        value = value[1:]
    match = re.match(r"^(\d+)\.(\d+)\.(\d+)$", value)
    if not match:
        return None
    return tuple(int(part) for part in match.groups())


def list_release_note_versions():
    releases_dir = ROOT / "releases"
    if not releases_dir.is_dir():
        return []
    versions = []
    for path in releases_dir.glob("v*.md"):
        version = parse_version(path.stem)
        if version:
            versions.append(version)
    return versions


def get_previous_release_version(current_version: str):
    current = parse_version(current_version)
    if not current:
        return None
    candidates = [v for v in list_release_note_versions() if v < current]
    if not candidates:
        return None
    return max(candidates)


def format_version(version_tuple):
    return ".".join(str(part) for part in version_tuple)


def tag_exists(tag: str) -> bool:
    output = run_git(["tag", "--list", tag])
    return bool(output.strip())


def get_base_from_previous_notes(version_tuple):
    if not version_tuple:
        return None
    filename = f"v{format_version(version_tuple)}.md"
    path = ROOT / "releases" / filename
    if not path.is_file():
        return None
    text = path.read_text(encoding="utf-8").replace("\r\n", "\n")
    match = re.search(r"^## Commits\s*$\n(?P<body>.*?)(?=^## |\Z)", text, re.M | re.S)
    if not match:
        return None
    for line in match.group("body").splitlines():
        line = line.strip()
        if not line.startswith("-"):
            continue
        commit_match = re.search(r"/commit/([0-9a-f]{7,40})", line)
        if commit_match:
            return commit_match.group(1)
    return None


def get_commits(base, head):
    log = run_git(["log", "--pretty=format:%H\t%s", f"{base}..{head}"])
    rows = []
    for line in log.splitlines():
        line = line.strip()
        if not line:
            continue
        if "\t" in line:
            sha, subject = line.split("\t", 1)
        else:
            sha, subject = line, ""
        subject = subject.strip()
        if not subject:
            continue
        subject_lower = subject.lower()
        if subject_lower.startswith("merge"):
            continue
        if subject_lower.startswith("update changelog"):
            continue
        if len(subject.split()) <= 1:
            continue
        rows.append((sha, subject))
    return rows


def load_existing_description_block(path: Path):
    if not path.is_file():
        return None
    text = path.read_text(encoding="utf-8").replace("\r\n", "\n")
    match = re.search(r"^## Description\s*$\n(?P<desc>.*?)(?=^## |\Z)", text, re.M | re.S)
    if not match:
        return None
    return match.group(0).rstrip("\n")


def build_notes(version, base, head, description_block):
    today = date.today().isoformat()
    commits = get_commits(base, head)

    lines = []
    lines.append(f"# Release v{version}")
    lines.append(f"Date: {today}")
    lines.append("")
    if description_block:
        lines.extend(description_block.splitlines())
    else:
        lines.append("## Description")
        lines.append("TODO: Add release description.")
    lines.append("")

    lines.append("## Commits")
    if commits:
        for sha, subject in commits:
            short = sha[:7]
            label = subject or short
            lines.append(f"- [{label}](https://github.com/divengine/div/commit/{sha})")
    else:
        lines.append("- No commits found.")
    lines.append("")

    return "\n".join(lines).rstrip() + "\n"


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--version", default="")
    parser.add_argument("--base-tag", default="")
    parser.add_argument("--head", default="HEAD")
    parser.add_argument(
        "--output",
        default="",
        help="Output file path. Defaults to releases/v<version>.md",
    )
    args = parser.parse_args()

    version = args.version.strip() or get_version()
    base = args.base_tag.strip()
    if not base:
        prev_version = get_previous_release_version(version)
        if prev_version:
            prev_tag = f"v{format_version(prev_version)}"
            if tag_exists(prev_tag):
                base = prev_tag
            else:
                base = get_base_from_previous_notes(prev_version)
        if not base:
            base = get_last_tag()
    head = args.head.strip() or "HEAD"

    output = args.output.strip()
    if not output:
        output = f"releases/v{version}.md"

    out_path = (ROOT / output).resolve()
    out_path.parent.mkdir(parents=True, exist_ok=True)

    existing_description = load_existing_description_block(out_path)
    notes = build_notes(version, base, head, existing_description)
    out_path.write_text(notes, encoding="utf-8", newline="\n")

    print(f"Release notes written to {out_path}")


if __name__ == "__main__":
    main()
