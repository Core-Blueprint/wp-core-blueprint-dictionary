<?php
declare(strict_types=1);

namespace CB\Dictionary;

use CB\Dictionary\Content\Alphabet;
use CB\Dictionary\Content\Meta;
use CB\Dictionary\Content\PostType;
use CB\Dictionary\Content\Taxonomies;

defined( 'ABSPATH' ) || exit;

final class Install {
	public static function activate(): void {
		PostType::register();
		Taxonomies::register();
		Meta::register();
		Alphabet::seed_terms();
		delete_option( Settings::REWRITE_DIRTY_OPTION );
		flush_rewrite_rules();
	}

	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
