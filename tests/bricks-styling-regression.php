<?php
declare(strict_types=1);

$root = dirname( __DIR__ );
$failures = [];

/** @param bool $condition */
function cbd_styling_assert( bool $condition, string $message ): void {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: $message\n" );
		exit( 1 );
	}
}

/** @return string[] */
function cbd_styling_selector_classes( string $content ): array {
	preg_match_all( "/'selector'\\s*=>\\s*'([^']+)'/", $content, $selectors );
	$classes = [];
	foreach ( $selectors[1] ?? [] as $selector ) {
		preg_match_all( '/\\.([a-zA-Z0-9_-]+)/', (string) $selector, $matches );
		foreach ( $matches[1] ?? [] as $class ) {
			$classes[] = (string) $class;
		}
	}
	return array_values( array_unique( $classes ) );
}

/** Return one Bricks control block from an element source file. */
function cbd_styling_control_block( string $content, string $name ): string {
	$marker = "\t\t\$this->controls['" . $name . "'] = [";
	$start  = strpos( $content, $marker );
	cbd_styling_assert( false !== $start, 'Missing Bricks control: ' . $name );

	$next   = strpos( $content, "\n\t\t\$this->controls[", (int) $start + strlen( $marker ) );
	$render = strpos( $content, "\n\t}\n\n\tpublic function render", (int) $start + strlen( $marker ) );
	$end    = false !== $next ? $next : $render;
	cbd_styling_assert( false !== $end, 'Could not delimit Bricks control: ' . $name );

	return substr( $content, (int) $start, (int) $end - (int) $start );
}

$element_components = [
	'Search.php' => [
		'src/Frontend/Components/Search.php',
		'src/Frontend/Components/SearchResults.php',
	],
	'SearchResults.php' => [
		'src/Frontend/Components/SearchResults.php',
	],
	'Alphabet.php' => [
		'src/Frontend/Components/Alphabet.php',
	],
	'Entries.php' => [
		'src/Frontend/Components/Entries.php',
	],
	'EntryData.php' => [
		'src/Frontend/Components/Meta.php',
	],
];

foreach ( $element_components as $element_file => $component_files ) {
	$element = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/' . $element_file );
	$markup  = '';
	foreach ( $component_files as $component_file ) {
		$markup .= "\n" . (string) file_get_contents( $root . '/' . $component_file );
	}

	cbd_styling_assert(
		! preg_match( "/'type'\\s*=>\\s*'slider'/", $element ),
		$element_file . ' must use native Bricks number + units controls for CSS lengths, not slider controls'
	);

	foreach ( cbd_styling_selector_classes( $element ) as $class ) {
		$dynamic_search_modifier = 'Search.php' === $element_file
			&& str_starts_with( $class, 'cb-dictionary-search__submit--' )
			&& str_contains( $markup, "'cb-dictionary-search__submit--'" );

		cbd_styling_assert(
			$dynamic_search_modifier || str_contains( $markup, $class ),
			$element_file . ' styles class .' . $class . ' that is absent from its rendered component markup'
		);
	}

	$background_count = preg_match_all( "/'type'\\s*=>\\s*'background'/", $element );
	$video_excludes   = preg_match_all( "/'exclude'\\s*=>\\s*\\[\\s*'videoUrl',\\s*'videoScale'\\s*\\]/", $element );
	cbd_styling_assert(
		$background_count === $video_excludes,
		$element_file . ' must exclude unsupported background-video settings from every internal Background control'
	);
}

$native_length_controls = [
	'Search.php' => [
		'inputMinHeight',
		'buttonOverlayInset',
		'buttonIconGap',
		'buttonIconSize',
		'buttonMinHeight',
		'formColumnGap',
		'formRowGap',
		'formGridGap',
		'resultsListColumnGap',
		'resultsListRowGap',
		'resultsGridGap',
	],
	'SearchResults.php' => [
		'listColumnGap',
		'listRowGap',
		'listGridGap',
	],
	'Alphabet.php' => [
		'listColumnGap',
		'listRowGap',
		'listGridGap',
	],
	'Entries.php' => [
		'listColumnGap',
		'listRowGap',
		'listGridGap',
	],
	'EntryData.php' => [
		'metaColumnGap',
		'metaRowGap',
		'metaGridGap',
		'rowColumnGap',
		'rowRowGap',
		'rowGridGap',
		'labelWidth',
	],
];

foreach ( $native_length_controls as $element_file => $control_names ) {
	$content = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/' . $element_file );
	foreach ( $control_names as $control_name ) {
		$block = cbd_styling_control_block( $content, $control_name );
		cbd_styling_assert(
			(bool) preg_match( "/'type'\\s*=>\\s*'number'/", $block ),
			$element_file . ' control ' . $control_name . ' must use native Bricks number control'
		);
		cbd_styling_assert(
			(bool) preg_match( "/'units'\\s*=>\\s*true/", $block ),
			$element_file . ' control ' . $control_name . ' must enable native Bricks units'
		);
	}
}

$flex_contracts = [
	'Search.php' => [
		[ 'form', 'formDisplay' ],
		[ 'resultsList', 'resultsListDisplay' ],
	],
	'SearchResults.php' => [
		[ 'list', 'listDisplay' ],
	],
	'Alphabet.php' => [
		[ 'list', 'listDisplay' ],
	],
	'Entries.php' => [
		[ 'list', 'listDisplay' ],
	],
	'EntryData.php' => [
		[ 'meta', 'metaDisplay' ],
		[ 'row', 'rowDisplay' ],
	],
];

foreach ( $flex_contracts as $element_file => $contracts ) {
	$content = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/' . $element_file );
	foreach ( $contracts as [ $prefix, $display_control ] ) {
		foreach ( [
			$prefix . 'FlexWrap'        => 'flex-wrap',
			$prefix . 'Direction'       => 'flex-direction',
			$prefix . 'JustifyContent'  => 'justify-content',
			$prefix . 'AlignItems'      => 'align-items',
			$prefix . 'ColumnGap'       => 'column-gap',
			$prefix . 'RowGap'          => 'row-gap',
		] as $control_name => $property ) {
			$block = cbd_styling_control_block( $content, $control_name );
			cbd_styling_assert(
				str_contains( $block, "'property' => '" . $property . "'" ),
				$element_file . ' control ' . $control_name . ' must target ' . $property
			);
			cbd_styling_assert(
				str_contains( $block, "'" . $display_control . "'" )
					&& str_contains( $block, "'flex'" ),
				$element_file . ' control ' . $control_name . ' must only appear for flex display'
			);
		}
	}
}

$search = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/Search.php' );
foreach ( [
	"'resultsListMargin'",
	"'resultsListPadding'",
	"'formFlexWrap'",
	"'formGridGap'",
	"'resultsListFlexWrap'",
	"'resultsGridGap'",
	"[ [ 'resultsMode', '=', 'inline' ], [ 'showExcerpt', '=', true ] ]",
	"[ [ 'resultsMode', '=', 'inline' ], [ 'showCount', '=', true ] ]",
	"[ [ 'buttonMode', '!=', 'hidden' ], [ 'buttonPlacement', '=', 'overlay' ] ]",
] as $needle ) {
	cbd_styling_assert( str_contains( $search, $needle ), 'Search Golden styling state contract missing: ' . $needle );
}

$search_results = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/SearchResults.php' );
foreach ( [ "'listMargin'", "'listPadding'", "'listFlexWrap'", "'listGridGap'", "[ 'showExcerpt', '=', true ]", "[ 'showCount', '=', true ]" ] as $needle ) {
	cbd_styling_assert( str_contains( $search_results, $needle ), 'Search Results Golden styling state contract missing: ' . $needle );
}

$alphabet = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/Alphabet.php' );
foreach ( [ "'listMargin'", "'listPadding'", "'listFlexWrap'", "'listGridGap'", "[ 'showEmpty', '=', true ]" ] as $needle ) {
	cbd_styling_assert( str_contains( $alphabet, $needle ), 'Alphabet Golden styling state contract missing: ' . $needle );
}

$entries = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/Entries.php' );
foreach ( [ "'listMargin'", "'listPadding'", "'listFlexWrap'", "'listGridGap'", "[ 'showExcerpt', '=', true ]" ] as $needle ) {
	cbd_styling_assert( str_contains( $entries, $needle ), 'Entries Golden styling state contract missing: ' . $needle );
}

$entry_data = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/EntryData.php' );
foreach ( [
	"'rowColumns'",
	"'max-content minmax(0, 1fr)'",
	"'metaFlexWrap'",
	"'metaGridGap'",
	"'rowFlexWrap'",
	"'rowGridGap'",
] as $needle ) {
	cbd_styling_assert( str_contains( $entry_data, $needle ), 'Entry Data Golden styling state contract missing: ' . $needle );
}

$css = (string) file_get_contents( $root . '/assets/css/dictionary-search.css' );
foreach ( [
	'.cb-dictionary-search__input',
	'min-width: 0',
	'.cb-dictionary-search-results__count,',
	'.cb-dictionary-search-results__link,',
	'.cb-dictionary-alphabet__link,',
	'.cb-dictionary-list__excerpt > :first-child',
	'.cb-dictionary-list__excerpt > :last-child',
	'.cb-dictionary-meta__label,',
	'margin: 0',
] as $needle ) {
	cbd_styling_assert( str_contains( $css, $needle ), 'Dictionary component baseline missing: ' . $needle );
}

fwrite( STDOUT, "Dictionary Bricks styling regression: PASS\n" );
