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

$slider_unit_profiles = [
	'spacing_units',
	'size_units',
	'icon_units',
	'width_units',
];

$control_options = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/ControlOptions.php' );
foreach ( $slider_unit_profiles as $profile ) {
	cbd_styling_assert(
		str_contains( $control_options, 'function ' . $profile . '()' ),
		'Dictionary Bricks slider unit profile missing: ' . $profile
	);
}
foreach ( [ "'px'", "'rem'", "'em'", "'%'" ] as $unit ) {
	cbd_styling_assert(
		str_contains( $control_options, $unit ),
		'Dictionary Bricks slider unit configuration missing unit: ' . $unit
	);
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

	$control_blocks = preg_split( '/\\n\\t\\t\\$this->controls\\[/', $element ) ?: [];
	foreach ( $control_blocks as $control_block ) {
		if ( ! preg_match( "/'type'\\s*=>\\s*'slider'/", $control_block ) ) {
			continue;
		}

		cbd_styling_assert(
			str_contains( $control_block, "'units' => ControlOptions::" ),
			$element_file . ' contains a slider without explicit Bricks CSS units'
		);
		cbd_styling_assert(
			str_contains( $control_block, "'unitless' => false" ),
			$element_file . ' contains a CSS length slider that is still configured as unitless'
		);
	}

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

$search = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/Search.php' );
foreach ( [
	"'resultsListMargin'",
	"'resultsListPadding'",
	"[ [ 'resultsMode', '=', 'inline' ], [ 'resultsListDisplay', '=', 'grid' ] ]",
	"[ [ 'resultsMode', '=', 'inline' ], [ 'resultsListDisplay', '=', [ 'flex', 'grid' ] ] ]",
	"[ [ 'resultsMode', '=', 'inline' ], [ 'showExcerpt', '=', true ] ]",
	"[ [ 'resultsMode', '=', 'inline' ], [ 'showCount', '=', true ] ]",
	"[ [ 'buttonMode', '!=', 'hidden' ], [ 'buttonPlacement', '=', 'overlay' ] ]",
] as $needle ) {
	cbd_styling_assert( str_contains( $search, $needle ), 'Search Golden styling state contract missing: ' . $needle );
}

$search_results = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/SearchResults.php' );
foreach ( [ "'listMargin'", "'listPadding'", "[ 'listDisplay', '=', [ 'flex', 'grid' ] ]", "[ 'showExcerpt', '=', true ]", "[ 'showCount', '=', true ]" ] as $needle ) {
	cbd_styling_assert( str_contains( $search_results, $needle ), 'Search Results Golden styling state contract missing: ' . $needle );
}

$alphabet = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/Alphabet.php' );
foreach ( [ "'listMargin'", "'listPadding'", "[ 'showEmpty', '=', true ]" ] as $needle ) {
	cbd_styling_assert( str_contains( $alphabet, $needle ), 'Alphabet Golden styling state contract missing: ' . $needle );
}

$entries = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/Entries.php' );
foreach ( [ "'listMargin'", "'listPadding'", "[ 'listDisplay', '=', [ 'flex', 'grid' ] ]", "[ 'showExcerpt', '=', true ]" ] as $needle ) {
	cbd_styling_assert( str_contains( $entries, $needle ), 'Entries Golden styling state contract missing: ' . $needle );
}

$entry_data = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/EntryData.php' );
foreach ( [
	"'rowColumns'",
	"'max-content minmax(0, 1fr)'",
	"[ 'metaDisplay', '=', [ 'flex', 'grid' ] ]",
	"[ 'rowDisplay', '=', [ 'flex', 'grid' ] ]",
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
