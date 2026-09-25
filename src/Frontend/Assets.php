<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend;

defined( 'ABSPATH' ) || exit;

final class Assets {
	private const SEARCH_HANDLE = 'cb-dictionary-search';

	public static function init(): void {
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_search_styles' ] );
	}

	public static function enqueue_search_styles(): void {
		wp_enqueue_style(
			self::SEARCH_HANDLE,
			CB_DICTIONARY_URL . 'assets/css/dictionary-search.css',
			[],
			CB_DICTIONARY_VERSION
		);
	}

	public static function enqueue_search( bool $live = true ): void {
		self::enqueue_search_styles();

		if ( ! $live ) {
			return;
		}

		wp_enqueue_script(
			self::SEARCH_HANDLE,
			CB_DICTIONARY_URL . 'assets/js/dictionary-search.js',
			[],
			CB_DICTIONARY_VERSION,
			true
		);
	}
}
