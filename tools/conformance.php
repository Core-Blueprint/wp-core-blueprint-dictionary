<?php
declare(strict_types=1);

$root = dirname( __DIR__ );
$failures = [];

/** @return string[] */
function cb_dictionary_files_with_extension( string $directory, string $extension ): array {
	if ( ! is_dir( $directory ) ) {
		return [];
	}

	$files = [];
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $iterator as $file ) {
		if ( $file instanceof SplFileInfo && $file->isFile() && strtolower( $file->getExtension() ) === $extension ) {
			$files[] = $file->getPathname();
		}
	}
	sort( $files );
	return $files;
}

$expected = [
	'core-blueprint-dictionary.php',
	'src/Plugin.php',
	'src/Settings.php',
	'src/Install.php',
	'src/Content/PostType.php',
	'src/Content/Taxonomies.php',
	'src/Content/Meta.php',
	'src/Content/Alphabet.php',
	'src/Admin/EntryDetails.php',
	'src/Admin/SettingsPage.php',
	'src/Admin/IntegrationReadiness.php',
	'src/Frontend/Data.php',
	'src/Frontend/Queries.php',
	'src/Frontend/Conditions.php',
	'src/Frontend/Shortcodes.php',
	'src/Governance/Events.php',
	'src/Integration/Suite.php',
	'src/Integration/Builders/Bootstrap.php',
	'src/Integration/Builders/Readiness.php',
	'src/Integration/Builders/Bricks/Bootstrap.php',
	'src/Integration/Builders/Bricks/Context.php',
	'src/Integration/Builders/Bricks/DynamicData.php',
	'src/Integration/Builders/Bricks/Queries.php',
	'src/Integration/Builders/Bricks/Conditions.php',
	'src/Integration/Builders/Bricks/GroupOrder.php',
];
foreach ( $expected as $path ) {
	if ( ! is_file( $root . '/' . $path ) ) {
		$failures[] = 'Missing expected runtime file: ' . $path;
	}
}

$php_files = array_merge( [ $root . '/core-blueprint-dictionary.php' ], cb_dictionary_files_with_extension( $root . '/src', 'php' ) );
$forbidden = [
	'cb-core-css-' => 'private Base CSS handles are not public API',
	'cb_core_event_labels' => 'legacy event-label mutation is not the Governance contract',
	'CB\\Core\\Log\\AuditLog' => 'extensions must write through Governance\\Audit',
	'CB\\Core\\Admin\\AdminAssetCatalog' => 'the Base asset catalog is private',
	'Requires Plugins:' => 'first-party extensions use the runtime Base dependency guard',
	'jquery' => 'Dictionary has no jQuery runtime',
];
foreach ( $php_files as $file ) {
	$content = is_file( $file ) ? (string) file_get_contents( $file ) : '';
	foreach ( $forbidden as $needle => $reason ) {
		if ( false !== stripos( $content, $needle ) ) {
			$failures[] = sprintf( '%s contains forbidden pattern "%s" (%s).', str_replace( $root . '/', '', $file ), $needle, $reason );
		}
	}
}

$post_type = (string) file_get_contents( $root . '/src/Content/PostType.php' );
foreach ( [ "'custom-fields'", "'comments'", "'revisions'", "'show_in_rest'", 'Settings::rewrite_base()' ] as $required ) {
	if ( ! str_contains( $post_type, $required ) ) {
		$failures[] = 'Post type contract is missing ' . $required . '.';
	}
}

$taxonomies = (string) file_get_contents( $root . '/src/Content/Taxonomies.php' );
foreach ( [ 'cb_dictionary_category', 'cb_dictionary_tag', 'cb_dictionary_letter', "'assign_terms'", "'do_not_allow'", "'show_ui'" ] as $required ) {
	if ( ! str_contains( $taxonomies, $required ) ) {
		$failures[] = 'Taxonomy contract is missing ' . $required . '.';
	}
}

$alphabet = (string) file_get_contents( $root . '/src/Content/Alphabet.php' );
foreach ( [ "'0-9' => '0-9'", "range( 'A', 'Z' )", 'wp_set_object_terms', 'remove_accents' ] as $required ) {
	if ( ! str_contains( $alphabet, $required ) ) {
		$failures[] = 'Alphabet contract is missing ' . $required . '.';
	}
}

$settings = (string) file_get_contents( $root . '/src/Settings.php' );
foreach ( [ 'DEFAULT_REWRITE_BASE', 'REWRITE_DIRTY_OPTION', 'flush_rewrite_rules( false )', 'record_settings_updated', "'tab'                   => 'general'" ] as $required ) {
	if ( ! str_contains( $settings, $required ) ) {
		$failures[] = 'Settings/rewrite contract is missing ' . $required . '.';
	}
}

$events = (string) file_get_contents( $root . '/src/Governance/Events.php' );
foreach ( [ 'EventRegistry::register', 'Audit::record', 'dictionary.settings.updated' ] as $required ) {
	if ( ! str_contains( $events, $required ) ) {
		$failures[] = 'Governance contract is missing ' . $required . '.';
	}
}

$admin_page = (string) file_get_contents( $root . '/src/Admin/SettingsPage.php' );
foreach ( [ 'TAB_OVERVIEW', 'TAB_GENERAL', 'TAB_INTEGRATIONS', 'IntegrationGrid::render', "'metric-tiles'", "'nav-tabs'", "'integration-grid'" ] as $required ) {
	if ( ! str_contains( $admin_page, $required ) ) {
		$failures[] = 'Golden Admin contract is missing ' . $required . '.';
	}
}

$admin_readiness = (string) file_get_contents( $root . '/src/Admin/IntegrationReadiness.php' );
if ( ! str_contains( $admin_readiness, 'BuilderReadiness::bricks_active()' ) ) {
	$failures[] = 'Admin integration readiness must consume the builder readiness boundary.';
}
foreach ( [ 'BRICKS_VERSION', '\\Bricks\\' ] as $forbidden_admin_builder_reference ) {
	if ( str_contains( $admin_readiness, $forbidden_admin_builder_reference ) ) {
		$failures[] = 'Admin integration readiness must not detect Bricks directly: ' . $forbidden_admin_builder_reference;
	}
}

$builder_readiness = (string) file_get_contents( $root . '/src/Integration/Builders/Readiness.php' );
if ( ! str_contains( $builder_readiness, "defined( 'BRICKS_VERSION' )" ) ) {
	$failures[] = 'Builder readiness must own Bricks availability detection.';
}

$bootstrap = (string) file_get_contents( $root . '/core-blueprint-dictionary.php' );
foreach ( [ 'CB\\Core\\UI\\Notice', 'CB\\Core\\UI\\IntegrationGrid' ] as $required ) {
	if ( ! str_contains( $bootstrap, $required ) ) {
		$failures[] = 'Base public-contract gate is missing ' . $required . '.';
	}
}

$bricks_context = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Context.php' );
$bricks_queries = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Queries.php' );
$bricks_conditions = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Conditions.php' );
if ( ! str_contains( $bricks_context, 'Frontend\\Data' ) || ! str_contains( $bricks_context, 'Data::entry' ) ) {
	$failures[] = 'Bricks context must delegate data projection to the builder-neutral Frontend\\Data contract.';
}
if ( ! str_contains( $bricks_queries, 'FrontendQueries::entries' ) ) {
	$failures[] = 'Bricks queries must delegate to the builder-neutral Frontend\\Queries contract.';
}
if ( ! str_contains( $bricks_conditions, 'FrontendConditions::' ) ) {
	$failures[] = 'Bricks conditions must delegate to the builder-neutral Frontend\\Conditions contract.';
}

foreach ( cb_dictionary_files_with_extension( $root . '/src/Integration/Builders/Bricks', 'php' ) as $file ) {
	$content = (string) file_get_contents( $file );
	foreach ( [ 'new WP_Query', 'new \\WP_Query', 'get_post_meta(', 'update_post_meta(', 'wp_insert_post(', 'wp_update_post(' ] as $needle ) {
		if ( false !== stripos( $content, $needle ) ) {
			$failures[] = sprintf( '%s contains domain/storage logic forbidden in the Bricks adapter: %s.', str_replace( $root . '/', '', $file ), $needle );
		}
	}
}

if ( ! empty( $failures ) ) {
	fwrite( STDERR, "Core Blueprint Dictionary conformance: FAIL\n\n" );
	foreach ( $failures as $failure ) {
		fwrite( STDERR, '- ' . $failure . "\n" );
	}
	exit( 1 );
}

fwrite( STDOUT, "Core Blueprint Dictionary conformance: PASS\n" );
