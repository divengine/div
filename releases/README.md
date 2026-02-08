# Release Notes

Each release must have a markdown file in this folder named `v<version>.md`,
where `<version>` matches the `version` in `composer.json`.

To generate a draft with the deterministic sections, run:

```bash
python scripts/generate_release_notes.py
```

This draft includes:
- Description placeholder (preserved if already written)
- Commit subjects since the last tag

If the file already exists, the generator regenerates everything but preserves
the `## Description` section verbatim. The release workflow will fail if the
file is missing.
