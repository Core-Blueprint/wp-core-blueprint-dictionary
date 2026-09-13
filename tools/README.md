# Development and release tools

## Conformance

Run:

```bash
php tools/conformance.php
```

The check verifies expected runtime files, public Base boundaries, native CPT/taxonomy/meta structure, the machine-owned alphabet contract, Governance APIs and configurable rewrites.

## Localization

The only localization operator entrypoints are:

```bash
tools/i18n/update
tools/i18n/check
```

`update` regenerates/synchronizes the canonical source catalogs. `check` is the read-only release gate and proves current source/POT/PO conformance.

Dictionary is currently on a localization content hold: reviewed launch catalogs do not yet exist. The release builder therefore intentionally fails closed at `tools/i18n/check` until the required POT and six reviewed PO catalogs are present and valid. Do not bypass that gate with placeholder or machine-generated release translations.

## PHP syntax

```bash
find . -type f -name '*.php' -not -path './build/*' -not -path './dist/*' -print0 | xargs -0 -n1 php -l
```

## Release build

Run:

```bash
tools/build-release
```

The builder detects the version, requires the canonical localization gate, lints PHP, runs product conformance, stages one canonical `core-blueprint-dictionary/` root and creates `build/core-blueprint-dictionary-{version}.zip`.

Developer-only paths such as `.git`, `.github`, `tests`, `tools`, `docs`, `build`, `dist`, `node_modules` and `vendor` are excluded from and rejected in the customer ZIP.
