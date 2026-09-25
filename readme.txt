=== Core Blueprint Dictionary ===
Contributors: coreblueprint
Tags: dictionary, glossary, knowledge base, gutenberg, builder
Requires at least: 7.0
Requires PHP: 8.4
Stable tag: 1.0.0-rc1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight builder-agnostic digital dictionary using native WordPress content, taxonomies and metadata.

== Description ==

Core Blueprint Dictionary provides a ready-made WordPress dictionary model with user-managed Categories and Tags plus an automatically managed 0-9/A-Z alphabet.

Entries are normal WordPress content and can be edited in Gutenberg or consumed by builders such as Bricks without a builder-specific storage layer.

The public URL base is configurable under Core Blueprint > Dictionary and defaults to `dictionary`.

Core Blueprint Base with Core API 1.0 is required.

Dictionary stores its entries, taxonomies and metadata in native WordPress content structures. It does not send telemetry or tracking data to external services.

== Installation ==

1. Install and activate Core Blueprint Base.
2. Install and activate Core Blueprint Dictionary.
3. Add entries under Dictionary in WordPress Admin.
4. Configure the public URL base under Core Blueprint > Dictionary.
5. Build the frontend with your theme, Gutenberg, shortcodes or an optional supported builder adapter.

== Shortcodes ==

* `[cb_dictionary_list]`
* `[cb_dictionary_search]`
* `[cb_dictionary_alphabet]`
* `[cb_dictionary_categories]`
* `[cb_dictionary_meta]`

== Changelog ==

= 1.0.0-rc1 =
* Initial release candidate.
