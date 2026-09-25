<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend;

defined( 'ABSPATH' ) || exit;

final class Assets {
	private const SEARCH_HANDLE = 'cb-dictionary-search';

	public static function enqueue_search( bool $live = true ): void {
		wp_enqueue_style(
			self::SEARCH_HANDLE,
			CB_DICTIONARY_URL . 'assets/css/dictionary-search.css',
			[],
			CB_DICTIONARY_VERSION
		);

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
