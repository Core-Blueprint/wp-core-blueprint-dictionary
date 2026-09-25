# Core Blueprint Dictionary

Core Blueprint Dictionary is a lightweight, builder-agnostic digital dictionary extension for the Core Blueprint WordPress suite.

It provides a ready-made native WordPress dictionary model: install it, add terms, organise them, and build the frontend with Gutenberg, Bricks, another builder, a theme, REST consumers or normal WordPress code.

## v1.0.0-rc1 scope

- Native public `cb_dictionary` post type.
- Configurable public URL base, default `/dictionary/`.
- Gutenberg and standard WordPress support for title, content, excerpt, author, featured image, revisions, custom fields and comments.
- User-managed hierarchical `cb_dictionary_category` taxonomy.
- User-managed non-hierarchical `cb_dictionary_tag` taxonomy.
- Machine-owned `cb_dictionary_letter` taxonomy with fixed `0-9` and `A-Z` terms.
- Alphabet assignment automatically follows the entry title.
- Registered native post meta: `cb_dictionary_pronunciation`, `cb_dictionary_abbreviation`, `cb_dictionary_synonyms`, `cb_dictionary_source`, `cb_dictionary_featured`.
- WP-native Dictionary Details metabox.
- Minimal builder-agnostic shortcodes.
- Core Blueprint Base ExtensionRegistry, PageRegistry, health and Governance integration.
- No direct Core Blueprint Access dependency.
- No Bricks-specific runtime integration.
- No custom database tables or proprietary field storage.

## Taxonomy roles

**Categories** describe where an entry belongs and support hierarchy. **Tags** add flexible non-hierarchical topics. **Alphabet** is controlled by the plugin and is never manually assigned.

The alphabet contains exactly `0-9` and `A-Z`. Titles beginning with an accented Latin letter are normalised to the matching A-Z bucket. Numeric titles use `0-9`; unsupported leading symbols also fall back to `0-9`.

## URL base

Open **Core Blueprint → Dictionary** to change the default `dictionary` base. Examples include `woordenboek`, `begrippen`, `glossary` or a nested path such as `academy/dictionary`.

A changed URL base marks rewrites dirty and performs one rewrite flush only after the new routes have been registered on the next `init`.

## Shortcodes

- `[cb_dictionary_list]`
- `[cb_dictionary_search]`
- `[cb_dictionary_alphabet]`
- `[cb_dictionary_categories]`
- `[cb_dictionary_meta]`

## Governance events

- `dictionary.entry.created`
- `dictionary.entry.published`
- `dictionary.entry.updated`
- `dictionary.entry.trashed`
- `dictionary.entry.restored`
- `dictionary.entry.deleted`
- `dictionary.settings.updated`

Autosaves, revisions and auto-drafts are excluded. Multiple field/taxonomy changes in one request collapse into one update event.

## Requirements

- WordPress 7.0+
- PHP 8.4+
- Core Blueprint Base with Core API `1.0` or a compatible newer `1.x` minor
