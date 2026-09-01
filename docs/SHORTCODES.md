# Shortcodes

The shortcode layer is intentionally small. Builders may ignore it and query the native WordPress model directly.

## `[cb_dictionary_list]`

Lists published entries alphabetically by title.

Attributes:

- `category="slug"`
- `tag="slug"`
- `letter="a"` or `letter="0-9"`
- `limit="50"` — 1–100.
- `excerpt="true"`

## `[cb_dictionary_search]`

Renders a GET search form and scoped dictionary results.

Attributes: `placeholder="..."`, `limit="30"`. The query parameter is `cb_dictionary_q`.

## `[cb_dictionary_alphabet]`

Renders the machine-owned `0-9` and `A-Z` navigation. `show_empty="false"` hides empty buckets; `true` renders them as non-linked labels.

## `[cb_dictionary_categories]`

Renders top-level Dictionary Categories. Attribute: `hide_empty="true"`.

## `[cb_dictionary_meta]`

Renders available Dictionary metadata plus the automatically assigned Alphabet bucket. Optional attribute: `id="123"`.
