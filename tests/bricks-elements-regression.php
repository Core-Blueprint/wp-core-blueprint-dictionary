<?php
declare(strict_types=1);

namespace Bricks {
	class Element {}
	final class Elements {
		/** @var array<int,array{file:string,name:string,class:string}> */
		public static array $registered = [];

		public static function register_element( string $file, string $name, string $class ): void {
			self::$registered[] = compact( 'file', 'name', 'class' );
		}
	}
}

namespace {
	define( 'ABSPATH', __DIR__ . '/' );
	$GLOBALS['cbd_element_filters'] = [];

	function add_filter( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
		unset( $priority, $accepted_args );
		$GLOBALS['cbd_element_filters'][ $hook ][] = $callback;
	}

	function esc_html__( string $text, string $domain = 'default' ): string {
		unset( $domain );
		return $text;
	}

	function cbd_elements_assert( bool $condition, string $message ): void {
		if ( ! $condition ) {
			fwrite( STDERR, "FAIL: $message\n" );
			exit( 1 );
		}
	}

	require_once dirname( __DIR__ ) . '/src/Integration/Builders/Bricks/ElementRegistry.php';

	\CB\Dictionary\Integration\Builders\Bricks\ElementRegistry::register();

	$names = array_column( \Bricks\Elements::$registered, 'name' );
	$expected = [
		'cb-dictionary-search',
		'cb-dictionary-search-results',
		'cb-dictionary-alphabet',
		'cb-dictionary-entries',
		'cb-dictionary-entry-data',
	];

	cbd_elements_assert( $expected === $names, 'all Dictionary Bricks elements must register in stable order' );
	cbd_elements_assert( isset( $GLOBALS['cbd_element_filters']['bricks/builder/i18n'] ), 'Dictionary Bricks category i18n must register' );

	fwrite( STDOUT, "Dictionary Bricks elements regression: PASS\n" );
}
