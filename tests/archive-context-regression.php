<?php
declare(strict_types=1);

namespace {
	define( 'ABSPATH', __DIR__ . '/' );

	final class WP_Term {
		public function __construct(
			public string $taxonomy,
			public string $slug
		) {}
	}

	$GLOBALS['cbd_queried_object'] = null;

	function sanitize_title( string $value ): string {
		$value = strtolower( trim( $value ) );
		return trim( preg_replace( '/[^a-z0-9-]+/', '-', $value ) ?? '', '-' );
	}

	function get_queried_object(): mixed {
		return $GLOBALS['cbd_queried_object'];
	}

	function cbd_archive_assert( bool $condition, string $message ): void {
		if ( ! $condition ) {
			fwrite( STDERR, "FAIL: $message\n" );
			exit( 1 );
		}
	}

	require_once dirname( __DIR__ ) . '/src/Content/Taxonomies.php';
	require_once dirname( __DIR__ ) . '/src/Frontend/ArchiveContext.php';

	use CB\Dictionary\Content\Taxonomies;
	use CB\Dictionary\Frontend\ArchiveContext;

	$GLOBALS['cbd_queried_object'] = new WP_Term( Taxonomies::TAG, 'archive-tag' );
	cbd_archive_assert(
		[ 'category' => 'manual-category', 'tag' => '', 'letter' => '' ] === ArchiveContext::taxonomy_filters( 'Manual Category', '', '' ),
		'explicit filters must win over archive context as a set'
	);

	foreach ( [
		Taxonomies::CATEGORY => [ 'category', 'governance' ],
		Taxonomies::TAG      => [ 'tag', 'privacy' ],
		Taxonomies::LETTER   => [ 'letter', 'a' ],
	] as $taxonomy => [ $expected_key, $slug ] ) {
		$GLOBALS['cbd_queried_object'] = new WP_Term( $taxonomy, $slug );
		$filters = ArchiveContext::taxonomy_filters();
		cbd_archive_assert( $slug === $filters[ $expected_key ], $taxonomy . ' archive must resolve its current slug' );
		cbd_archive_assert( 1 === count( array_filter( $filters ) ), $taxonomy . ' archive must only add its own filter' );
	}

	$GLOBALS['cbd_queried_object'] = new WP_Term( 'category', 'not-dictionary' );
	cbd_archive_assert(
		[ 'category' => '', 'tag' => '', 'letter' => '' ] === ArchiveContext::taxonomy_filters(),
		'non-Dictionary taxonomy archives must not affect Dictionary Entries'
	);

	$GLOBALS['cbd_queried_object'] = null;
	cbd_archive_assert(
		[ 'category' => '', 'tag' => '', 'letter' => '' ] === ArchiveContext::taxonomy_filters(),
		'non-term contexts must not add filters'
	);

	fwrite( STDOUT, "Dictionary archive context regression: PASS\n" );
}
