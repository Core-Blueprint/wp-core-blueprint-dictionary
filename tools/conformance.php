<?php
declare(strict_types=1);

$root = dirname( __DIR__ );
$failures = [];

/** @return string[] */
function cb_dictionary_files_with_extension( string $directory, string $extension ): array {
	if ( ! is_dir( $directory ) ) { return []; }
	$files = [];
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $iterator as $file ) {
		if ( $file instanceof SplFileInfo && $file->isFile() && strtolower( $file->getExtension() ) === $extension ) { $files[] = $file->getPathname(); }
	}
	sort( $files );
	return $files;
}

$expected = [
	'core-blueprint-dictionary.php', 'src/Plugin.php', 'src/Settings.php', 'src/Install.php',
	'src/Content/PostType.php', 'src/Content/Taxonomies.php', 'src/Content/Meta.php', 'src/Content/Alphabet.php',
	'src/Admin/EntryDetails.php', 'src/Admin/SettingsPage.php', 'src/Frontend/Queries.php', 'src/Frontend/Shortcodes.php',
	'src/Governance/Events.php', 'src/Integration/Suite.php',
];
foreach ( $expected as $path ) { if ( ! is_file( $root . '/' . $path ) ) { $failures[] = 'Missing expected runtime file: ' . $path; } }

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
		if ( false !== stripos( $content, $needle ) ) { $failures[] = sprintf( '%s contains forbidden pattern "%s" (%s).', str_replace( $root . '/', '', $file ), $needle, $reason ); }
	}
}

$post_type = (string) file_get_contents( $root . '/src/Content/PostType.php' );
foreach ( [ "'custom-fields'", "'comments'", "'revisions'", "'show_in_rest'", 'Settings::rewrite_base()' ] as $required ) { if ( ! str_contains( $post_type, $required ) ) { $failures[] = 'Post type contract is missing ' . $required . '.'; } }

$taxonomies = (string) file_get_contents( $root . '/src/Content/Taxonomies.php' );
foreach ( [ 'cb_dictionary_category', 'cb_dictionary_tag', 'cb_dictionary_letter', "'assign_terms'", "'do_not_allow'", "'show_ui'" ] as $required ) { if ( ! str_contains( $taxonomies, $required ) ) { $failures[] = 'Taxonomy contract is missing ' . $required . '.'; } }

$alphabet = (string) file_get_contents( $root . '/src/Content/Alphabet.php' );
foreach ( [ "'0-9' => '0-9'", "range( 'A', 'Z' )", 'wp_set_object_terms', 'remove_accents' ] as $required ) { if ( ! str_contains( $alphabet, $required ) ) { $failures[] = 'Alphabet contract is missing ' . $required . '.'; } }

$settings = (string) file_get_contents( $root . '/src/Settings.php' );
foreach ( [ 'DEFAULT_REWRITE_BASE', 'REWRITE_DIRTY_OPTION', 'flush_rewrite_rules( false )', 'record_settings_updated' ] as $required ) { if ( ! str_contains( $settings, $required ) ) { $failures[] = 'Settings/rewrite contract is missing ' . $required . '.'; } }

$events = (string) file_get_contents( $root . '/src/Governance/Events.php' );
foreach ( [ 'EventRegistry::register', 'Audit::record', 'dictionary.settings.updated' ] as $required ) { if ( ! str_contains( $events, $required ) ) { $failures[] = 'Governance contract is missing ' . $required . '.'; } }

if ( ! empty( $failures ) ) {
	fwrite( STDERR, "Core Blueprint Dictionary conformance: FAIL\n\n" );
	foreach ( $failures as $failure ) { fwrite( STDERR, '- ' . $failure . "\n" ); }
	exit( 1 );
}
fwrite( STDOUT, "Core Blueprint Dictionary conformance: PASS\n" );
