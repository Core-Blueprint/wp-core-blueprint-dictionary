<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks;

defined( 'ABSPATH' ) || exit;

/** Registers Dictionary-specific Bricks elements without making Bricks a dependency. */
final class ElementRegistry {
	public const CATEGORY = 'core-blueprint-dictionary';

	/** @var array<string,array{name:string,class:class-string}> */
	private const ELEMENTS = [
		'Elements/Search.php' => [
			'name'  => 'cb-dictionary-search',
			'class' => Elements\Search::class,
		],
		'Elements/SearchResults.php' => [
			'name'  => 'cb-dictionary-search-results',
			'class' => Elements\SearchResults::class,
		],
		'Elements/Alphabet.php' => [
			'name'  => 'cb-dictionary-alphabet',
			'class' => Elements\Alphabet::class,
		],
		'Elements/Entries.php' => [
			'name'  => 'cb-dictionary-entries',
			'class' => Elements\Entries::class,
		],
		'Elements/EntryData.php' => [
			'name'  => 'cb-dictionary-entry-data',
			'class' => Elements\EntryData::class,
		],
	];

	public static function register(): void {
		if ( ! class_exists( '\\Bricks\\Elements' ) || ! class_exists( '\\Bricks\\Element' ) ) {
			return;
		}

		add_filter( 'bricks/builder/i18n', [ self::class, 'builder_i18n' ] );

		foreach ( self::ELEMENTS as $relative_file => $definition ) {
			$file = __DIR__ . '/' . $relative_file;
			if ( is_readable( $file ) ) {
				\Bricks\Elements::register_element( $file, $definition['name'], $definition['class'] );
			}
		}
	}

	/** @param array<string,string> $i18n @return array<string,string> */
	public static function builder_i18n( array $i18n ): array {
		$i18n[ self::CATEGORY ] = esc_html__( 'Core Blueprint Dictionary', 'core-blueprint-dictionary' );
		return $i18n;
	}
}
