<?php
declare(strict_types=1);

$root = dirname( __DIR__ );
$failures = [];

$files = [
	'src/Frontend/Search.php',
	'src/Frontend/RestSearch.php',
	'src/Frontend/Assets.php',
	'src/Frontend/Components/Search.php',
	'src/Frontend/Components/SearchResults.php',
	'assets/js/dictionary-search.js',
	'assets/css/dictionary-search.css',
];

foreach ( $files as $path ) {
	if ( ! is_file( $root . '/' . $path ) ) {
		$failures[] = 'Missing live search file: ' . $path;
	}
}

$provider = (string) file_get_contents( $root . '/src/Frontend/Search.php' );
foreach ( [ 'Meta::ABBREVIATION', 'Meta::SYNONYMS', 'Queries::entries', '1000', '950', '900' ] as $needle ) {
	if ( ! str_contains( $provider, $needle ) ) {
		$failures[] = 'Search ranking/provider contract missing: ' . $needle;
	}
}

$rest = (string) file_get_contents( $root . '/src/Frontend/RestSearch.php' );
foreach ( [ 'cb-dictionary/v1', "'/search'", 'WP_REST_Server::READABLE', "'permission_callback' => '__return_true'", 'no-store', "'Vary'", 'count_label' ] as $needle ) {
	if ( ! str_contains( $rest, $needle ) ) {
		$failures[] = 'REST search contract missing: ' . $needle;
	}
}

$search_component = (string) file_get_contents( $root . '/src/Frontend/Components/Search.php' );
foreach ( [ 'Assets::enqueue_search( $live )', 'data-live-search', 'data-endpoint', 'data-min-chars', 'data-button-mode', 'data-button-placement', 'data-button-side', 'role="combobox"', 'aria-autocomplete="list"', "'text-icon'", "'hidden'" ] as $needle ) {
	if ( ! str_contains( $search_component, $needle ) ) {
		$failures[] = 'Search component live-search contract missing: ' . $needle;
	}
}

$results_component = (string) file_get_contents( $root . '/src/Frontend/Components/SearchResults.php' );
foreach ( [ 'SearchProvider::entries', 'data-cb-dictionary-results', 'data-show-excerpt', 'data-show-count', 'role="listbox"', 'aria-selected="false"' ] as $needle ) {
	if ( ! str_contains( $results_component, $needle ) ) {
		$failures[] = 'Search results component contract missing: ' . $needle;
	}
}

$css = (string) file_get_contents( $root . '/assets/css/dictionary-search.css' );
foreach ( [ 'data-button-placement="overlay"', 'cb-dictionary-search__submit--overlay', 'cb-dictionary-search__submit--hidden', '--cb-dictionary-search-button-inset' ] as $needle ) {
	if ( ! str_contains( $css, $needle ) ) {
		$failures[] = 'Search presentation CSS contract missing: ' . $needle;
	}
}

$js = (string) file_get_contents( $root . '/assets/js/dictionary-search.js' );
foreach ( [ 'AbortController', 'setTimeout(() => search(term), 250)', "event.key === 'ArrowDown'", "event.key === 'ArrowUp'", "event.key === 'Enter'", "event.key === 'Escape'", 'textContent', 'replaceChildren', 'credentials: \'same-origin\'' ] as $needle ) {
	if ( ! str_contains( $js, $needle ) ) {
		$failures[] = 'Live search JavaScript contract missing: ' . $needle;
	}
}
if ( str_contains( $js, 'innerHTML' ) ) {
	$failures[] = 'Live search JavaScript must not render result payloads through innerHTML.';
}

if ( [] !== $failures ) {
	fwrite( STDERR, "Dictionary live search regression: FAIL\n\n" );
	foreach ( $failures as $failure ) {
		fwrite( STDERR, '- ' . $failure . "\n" );
	}
	exit( 1 );
}

fwrite( STDOUT, "Dictionary live search regression: PASS\n" );
