<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration;

use CB\Core\ExtensionRegistry;
use CB\Dictionary\Content\PostType;

defined( 'ABSPATH' ) || exit;

final class Suite {
	public const ID = 'core-blueprint-dictionary';

	public static function init(): void {
		add_action( 'cb_core_register_extensions', [ __CLASS__, 'register_extension' ] );
		add_filter( 'cb_core_module_status_definitions', [ __CLASS__, 'register_status_definition' ] );
	}

	public static function register_extension(): void {
		ExtensionRegistry::register( [
			'id' => self::ID,
			'plugin_file' => CB_DICTIONARY_BASENAME,
			'requires_api' => CB_DICTIONARY_REQUIRED_API,
			'menu_url' => admin_url( 'edit.php?post_type=' . PostType::TYPE ),
			'status_id' => self::ID,
		] );
	}

	/** @param array<string,array<string,mixed>> $definitions @return array<string,array<string,mixed>> */
	public static function register_status_definition( array $definitions ): array {
		$definitions[ self::ID ] = [ 'provider' => [ __CLASS__, 'status' ], 'label' => self::i18n_ready() ? __( 'Dictionary', 'core-blueprint-dictionary' ) : 'Dictionary', 'url' => admin_url( 'edit.php?post_type=' . PostType::TYPE ) ];
		return $definitions;
	}

	/** @return array{state:string,detail:string,url:string} */
	public static function status(): array {
		$counts = wp_count_posts( PostType::TYPE );
		$published = (int) ( $counts->publish ?? 0 );
		$drafts = (int) ( $counts->draft ?? 0 );
		return [
			'state' => 'ok',
			'detail' => sprintf( __( '%1$d published · %2$d drafts', 'core-blueprint-dictionary' ), $published, $drafts ),
			'url' => admin_url( 'edit.php?post_type=' . PostType::TYPE ),
		];
	}

	private static function i18n_ready(): bool {
		return did_action( 'init' ) > 0 || doing_action( 'init' );
	}
}
