# Architecture

## Product boundary

Core Blueprint Dictionary is a ready-made dictionary content model, not a second content-model framework.

It owns one WordPress post type, three WordPress taxonomies and five registered post-meta fields. Customer content remains in standard WordPress storage.

## Taxonomy ownership

`cb_dictionary_category` and `cb_dictionary_tag` are user-owned taxonomies.

`cb_dictionary_letter` is machine-owned. Dictionary seeds `0-9` and `A-Z` and assigns exactly one bucket from the entry title. The taxonomy stays public/queryable and REST-readable for builders, while management and assignment capabilities are deliberately blocked.

## Base boundary

Dictionary requires Core Blueprint Base and consumes public Base contracts:

- `CB_CORE_API_VERSION`
- `CB\Core\ExtensionRegistry`
- `CB\Core\Admin\PageRegistry`
- `CB\Core\Governance\EventRegistry`
- `CB\Core\Governance\Audit`

## Content Models boundary

The optional Base Content Models module is not a runtime dependency. Dictionary registers its fixed domain schema directly through WordPress.

## Access boundary

Dictionary has no direct dependency on Core Blueprint Access. Its public CPT can be discovered by Access through the generic public-post-type scope.

## Presentation boundary

WordPress data is the primary interface. Shortcodes are small fallback presentation primitives with `cb-dictionary-*` classes. Dictionary does not take over theme templates and does not ship builder-specific integrations.

## Permalinks

The default base is `dictionary`. The setting may contain nested sanitized path segments. Single, archive, category, tag and letter routes all follow the configured base.

Rewrite rules are flushed only on activation/deactivation or once after an actual URL-base change.
