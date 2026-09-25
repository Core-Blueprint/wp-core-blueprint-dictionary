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
		'cb-dictionary-categories',
		'cb-dictionary-entries',
		'cb-dictionary-entry-data',
	];

	cbd_elements_assert( $expected === $names, 'all Dictionary Bricks elements must register in stable order' );
	cbd_elements_assert( isset( $GLOBALS['cbd_element_filters']['bricks/builder/i18n'] ), 'Dictionary Bricks category i18n must register' );

	$root = dirname( __DIR__ );
	$custom_control_contracts = [
		'Search.php' => [
			"'liveSearch'",
			"'minChars'",
			"'buttonMode'",
			"'buttonText'",
			"'submitIcon'",
			"'buttonIconPosition'",
			"'buttonPlacement'",
			"'buttonOverlaySide'",
			"'buttonOverlayInset'",
			"'buttonIconSize'",
			"'buttonHoverIconColor'",
			"'formFlexWrap'",
			"'formDirection'",
			"'formJustifyContent'",
			"'formAlignItems'",
			"'formColumnGap'",
			"'formRowGap'",
			"'formGridGap'",
			"'inputFocusBorder'",
			"'buttonHoverBackground'",
			"'resultsListDisplay'",
			"'resultsListStyleType'",
			"'resultsListMargin'",
			"'resultsListPadding'",
			"'resultsListFlexWrap'",
			"'resultsListDirection'",
			"'resultsListJustifyContent'",
			"'resultsListAlignItems'",
			"'resultsListColumnGap'",
			"'resultsListRowGap'",
			"'resultsGridGap'",
			"'resultItemBackground'",
			"'resultItemSelectedBackground'",
			"'resultTitleSelectedColor'",
			"'countTypography'",
		],
		'SearchResults.php' => [
			"'containerBackground'",
			"'listDisplay'",
			"'listStyleType'",
			"'listMargin'",
			"'listPadding'",
			"'listColumns'",
			"'listFlexWrap'",
			"'listDirection'",
			"'listJustifyContent'",
			"'listAlignItems'",
			"'listColumnGap'",
			"'listRowGap'",
			"'listGridGap'",
			"'itemShadow'",
			"'itemSelectedBackground'",
			"'titleHoverColor'",
			"'titleSelectedColor'",
			"'statusTypography'",
		],
		'Alphabet.php' => [
			"'listDisplay'",
			"'listStyleType'",
			"'listMargin'",
			"'listPadding'",
			"'listFlexWrap'",
			"'listDirection'",
			"'listJustifyContent'",
			"'listAlignItems'",
			"'listColumnGap'",
			"'listRowGap'",
			"'listGridGap'",
			"'hoverBackground'",
			"'currentTypography'",
			"'emptyTypography'",
			"'emptyOpacity'",
		],
		'Categories.php' => [
			"'showEmpty'",
			"'listDisplay'",
			"'listStyleType'",
			"'listMargin'",
			"'listPadding'",
			"'listColumns'",
			"'listFlexWrap'",
			"'listDirection'",
			"'listJustifyContent'",
			"'listAlignItems'",
			"'listColumnGap'",
			"'listRowGap'",
			"'listGridGap'",
			"'linkTypography'",
			"'linkHoverColor'",
			"'linkFocusColor'",
			"'currentTypography'",
			"'emptyTypography'",
			"'emptyOpacity'",
		],
		'Entries.php' => [
			"'listDisplay'",
			"'listStyleType'",
			"'listMargin'",
			"'listPadding'",
			"'listColumns'",
			"'listFlexWrap'",
			"'listDirection'",
			"'listJustifyContent'",
			"'listAlignItems'",
			"'listColumnGap'",
			"'listRowGap'",
			"'listGridGap'",
			"'itemShadow'",
			"'linkHoverColor'",
			"'excerptSpacing'",
			"'emptyPadding'",
		],
		'EntryData.php' => [
			"'fields'",
			"'multiple'    => true",
			'MetaComponent::field_options()',
			'MetaComponent::DEFAULT_FIELDS',
			"'metaDisplay'",
			"'metaColumns'",
			"'metaFlexWrap'",
			"'metaDirection'",
			"'metaJustifyContent'",
			"'metaAlignItems'",
			"'metaColumnGap'",
			"'metaRowGap'",
			"'metaGridGap'",
			"'rowDisplay'",
			"'rowColumns'",
			"'rowFlexWrap'",
			"'rowDirection'",
			"'rowJustifyContent'",
			"'rowAlignItems'",
			"'rowColumnGap'",
			"'rowRowGap'",
			"'rowGridGap'",
			"'rowShadow'",
			"'labelWidth'",
			"'valuePadding'",
			'Context::entry_id()',
		],
	];

	foreach ( $custom_control_contracts as $file => $needles ) {
		$content = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/' . $file );
		foreach ( $needles as $needle ) {
			cbd_elements_assert( str_contains( $content, $needle ), $file . ' missing custom control contract ' . $needle );
		}
		if ( 'EntryData.php' !== $file ) {
			cbd_elements_assert(
				str_contains( $content, "'property' => 'list-style-type'" ),
				$file . ' must expose list-style-type for its semantic list output'
			);
		}
		if ( 'Search.php' === $file ) {
			foreach ( [ "'type'    => 'icon'", "method_exists( self::class, 'render_icon' )", "'render_control_icon'", "'button_mode'", "'button_placement'" ] as $search_contract ) {
				cbd_elements_assert( str_contains( $content, $search_contract ), 'Search.php missing Golden search-button contract ' . $search_contract );
			}
			cbd_elements_assert(
				substr_count( $content, "method_exists(" ) >= 2,
				'Search.php must guard both supported Bricks icon rendering paths with method_exists'
			);
		}
		cbd_elements_assert(
			! str_contains( $content, "'tab'   => 'style'" )
				&& ! str_contains( $content, "'tab'     => 'style'" )
				&& ! str_contains( $content, "'tab'      => 'style'" ),
			$file . ' must keep Dictionary-specific controls under the Content tab'
		);
	}

	$alphabet_source = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/Alphabet.php' );
	cbd_elements_assert( ! str_contains( $alphabet_source, "\$this->controls['justifyContent']" ), 'Alphabet must not retain obsolete duplicate justifyContent control' );
	cbd_elements_assert( ! str_contains( $alphabet_source, "\$this->controls['alignItems']" ), 'Alphabet must not retain obsolete duplicate alignItems control' );

	fwrite( STDOUT, "Dictionary Bricks elements regression: PASS\n" );
}
