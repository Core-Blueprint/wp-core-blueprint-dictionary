<?php
declare(strict_types=1);

namespace CB\Dictionary;

use CB\Dictionary\Admin\EntryDetails;
use CB\Dictionary\Admin\SettingsPage;
use CB\Dictionary\Content\Alphabet;
use CB\Dictionary\Content\Meta;
use CB\Dictionary\Content\PostType;
use CB\Dictionary\Content\Taxonomies;
use CB\Dictionary\Frontend\Assets;
use CB\Dictionary\Frontend\RestSearch;
use CB\Dictionary\Frontend\Shortcodes;
use CB\Dictionary\Governance\Events;
use CB\Dictionary\Integration\Builders\Bootstrap as BuilderBootstrap;
use CB\Dictionary\Integration\Suite;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		Suite::init();
		Events::init();
		Settings::init();

		add_action( 'init', [ PostType::class, 'register' ], 5 );
		add_action( 'init', [ Taxonomies::class, 'register' ], 6 );
		add_action( 'init', [ Meta::class, 'register' ], 7 );

		Alphabet::init();
		Assets::init();
		Shortcodes::init();
		RestSearch::init();
		BuilderBootstrap::init();

		if ( is_admin() ) {
			EntryDetails::init();
			SettingsPage::init();
		}

		add_filter( 'plugin_action_links_' . CB_DICTIONARY_BASENAME, [ __CLASS__, 'action_links' ] );
	}

	/** @param string[] $links @return string[] */
	public static function action_links( array $links ): array {
		$links[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'edit.php?post_type=' . PostType::TYPE ) ),
			esc_html__( 'Dictionary', 'core-blueprint-dictionary' )
		);
		$links[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( SettingsPage::url() ),
			esc_html__( 'Settings', 'core-blueprint-dictionary' )
		);
		return $links;
	}
}
