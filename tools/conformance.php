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
	'src/Frontend/Search.php',
	'src/Frontend/RestSearch.php',
	'src/Frontend/Assets.php',
	'src/Frontend/Components/Entries.php',
	'src/Frontend/Components/Alphabet.php',
	'src/Frontend/Components/Meta.php',
	'src/Frontend/Components/Search.php',
	'src/Frontend/Components/SearchResults.php',
	'src/Governance/Events.php',
	'src/Integration/Suite.php',
	'src/Integration/Builders/Bootstrap.php',
	'src/Integration/Builders/Readiness.php',
	'src/Integration/Builders/Bricks/Bootstrap.php',
	'src/Integration/Builders/Bricks/ControlOptions.php',
	'src/Integration/Builders/Bricks/ElementRegistry.php',
	'src/Integration/Builders/Bricks/Elements/Search.php',
	'src/Integration/Builders/Bricks/Elements/SearchResults.php',
	'src/Integration/Builders/Bricks/Elements/Alphabet.php',
	'src/Integration/Builders/Bricks/Elements/Entries.php',
	'src/Integration/Builders/Bricks/Elements/EntryData.php',
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
foreach ( [ 'DEFAULT_REWRITE_BASE', 'REWRITE_DIRTY_OPTION', 'flush_rewrite_rules( false )', 'record_settings_updated', 'SettingsPage::url(', "'general'", 'cb_dictionary_updated' ] as $required ) {
	if ( ! str_contains( $settings, $required ) ) {
		$failures[] = 'Settings/rewrite contract is missing ' . $required . '.';
	}
}
if ( str_contains( $settings, "'page'                  => SettingsPage::SLUG" ) ) {
	$failures[] = 'Settings save flow must not redirect to the removed flat settings route.';
}

$shortcodes = (string) file_get_contents( $root . '/src/Frontend/Shortcodes.php' );
foreach ( [
	'EntriesComponent::render',
	'SearchComponent::render',
	'SearchResultsComponent::render',
	'AlphabetComponent::render',
	'MetaComponent::render',
	'cb_dictionary_search_results',
] as $required ) {
	if ( ! str_contains( $shortcodes, $required ) ) {
		$failures[] = 'Builder-neutral shortcode/component contract is missing ' . $required . '.';
	}
}

$meta_component = (string) file_get_contents( $root . '/src/Frontend/Components/Meta.php' );
foreach ( [ 'DEFAULT_FIELDS', "'categories'", "'tags'", 'normalize_fields', "'fields'" ] as $required ) {
	if ( ! str_contains( $meta_component, $required ) ) {
		$failures[] = 'Dictionary Entry Data field-selection contract is missing ' . $required . '.';
	}
}

$bricks_control_options = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/ControlOptions.php' );
foreach ( [ 'spacing_units', 'size_units', 'icon_units', 'width_units' ] as $required ) {
	if ( ! str_contains( $bricks_control_options, 'function ' . $required . '()' ) ) {
		$failures[] = 'Dictionary Bricks slider unit profile is missing ' . $required . '.';
	}
}

$entry_data_element = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/EntryData.php' );
foreach ( [ "'fields'", "'multiple'    => true", 'MetaComponent::field_options()', 'MetaComponent::DEFAULT_FIELDS' ] as $required ) {
	if ( ! str_contains( $entry_data_element, $required ) ) {
		$failures[] = 'Dictionary Entry Data Bricks multiselect contract is missing ' . $required . '.';
	}
}

$search_provider = (string) file_get_contents( $root . '/src/Frontend/Search.php' );
foreach ( [ 'Meta::ABBREVIATION', 'Meta::SYNONYMS', 'Queries::entries', 'str_starts_with' ] as $required ) {
	if ( ! str_contains( $search_provider, $required ) ) {
		$failures[] = 'Dictionary search provider is missing ' . $required . '.';
	}
}

$search_component = (string) file_get_contents( $root . '/src/Frontend/Components/Search.php' );
foreach ( [ "'button_mode'", "'button_icon_position'", "'button_placement'", "'button_overlay_side'", 'data-button-mode', 'data-button-placement' ] as $required ) {
	if ( ! str_contains( $search_component, $required ) ) {
		$failures[] = 'Dictionary search presentation contract is missing ' . $required . '.';
	}
}
if ( str_contains( $search_component, '\\Bricks\\' ) ) {
	$failures[] = 'Builder-neutral Dictionary Search component must not depend on Bricks.';
}

$frontend_assets = (string) file_get_contents( $root . '/src/Frontend/Assets.php' );
foreach ( [ "add_action( 'wp_enqueue_scripts'", 'enqueue_search_styles' ] as $required ) {
	if ( ! str_contains( $frontend_assets, $required ) ) {
		$failures[] = 'Dictionary search asset bootstrap is missing ' . $required . '.';
	}
}

$entry_data_element = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/EntryData.php' );
if ( ! str_contains( $entry_data_element, 'Context::entry_id()' ) ) {
	$failures[] = 'Dictionary Entry Data must resolve the active Bricks Dictionary context when Entry ID is 0.';
}

$rest_search = (string) file_get_contents( $root . '/src/Frontend/RestSearch.php' );
foreach ( [ 'cb-dictionary/v1', 'WP_REST_Server::READABLE', "'permission_callback' => '__return_true'", "'Cache-Control'", "'Vary'" ] as $required ) {
	if ( ! str_contains( $rest_search, $required ) ) {
		$failures[] = 'Dictionary REST search contract is missing ' . $required . '.';
	}
}

$bricks_styling_css = (string) file_get_contents( $root . '/assets/css/dictionary-search.css' );
foreach ( [ '.cb-dictionary-search-results__link,', '.cb-dictionary-alphabet__link,', '.cb-dictionary-meta__label,', 'margin: 0' ] as $required ) {
	if ( ! str_contains( $bricks_styling_css, $required ) ) {
		$failures[] = 'Dictionary Bricks styling baseline is missing ' . $required . '.';
	}
}

foreach ( [ 'assets/js/dictionary-search.js', 'assets/css/dictionary-search.css' ] as $asset ) {
	if ( ! is_file( $root . '/' . $asset ) ) {
		$failures[] = 'Dictionary live search asset is missing ' . $asset . '.';
	}
}

$plugin_runtime = (string) file_get_contents( $root . '/src/Plugin.php' );
if ( ! str_contains( $plugin_runtime, 'RestSearch::init()' ) ) {
	$failures[] = 'Dictionary plugin must initialize the public live search REST endpoint.';
}

$events = (string) file_get_contents( $root . '/src/Governance/Events.php' );
foreach ( [ 'EventRegistry::register', 'Audit::record', 'dictionary.settings.updated' ] as $required ) {
	if ( ! str_contains( $events, $required ) ) {
		$failures[] = 'Governance contract is missing ' . $required . '.';
	}
}

$admin_page = (string) file_get_contents( $root . '/src/Admin/SettingsPage.php' );
foreach ( [
	'TAB_OVERVIEW',
	'TAB_GENERAL',
	'TAB_INTEGRATIONS',
	'IntegrationGrid::render',
	'class_exists( IntegrationGrid::class )',
	'class_exists( Notice::class )',
	'render_feedback',
	"'metric-tiles'",
	"'nav-tabs'",
	"'integration-grid'",
	'cb_core_register_settings',
	'SettingsRegistry::register',
	'SettingsRegistry::GROUP_CONTENT_PUBLISHING',
	'SettingsRegistry::url',
	'Suite::ID',
] as $required ) {
	if ( ! str_contains( $admin_page, $required ) ) {
		$failures[] = 'Golden Admin / Settings Hub contract is missing ' . $required . '.';
	}
}
foreach ( [ 'cb_core_register_pages', 'PageRegistry::register', 'core-blueprint-dictionary-settings', 'implements PageContract' ] as $legacy_settings_contract ) {
	if ( str_contains( $admin_page, $legacy_settings_contract ) ) {
		$failures[] = 'Dictionary settings must not retain the flat PageRegistry contract: ' . $legacy_settings_contract . '.';
	}
}

$plugin = (string) file_get_contents( $root . '/src/Plugin.php' );
if ( ! str_contains( $plugin, 'SettingsPage::url()' ) ) {
	$failures[] = 'Plugin Settings action link must target the canonical Settings Hub provider URL.';
}
if ( str_contains( $plugin, 'SettingsPage::SLUG' ) ) {
	$failures[] = 'Plugin Settings action link must not target the removed flat settings slug.';
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
foreach ( [ 'Notice', 'IntegrationGrid' ] as $presentation_only_contract ) {
	if ( str_contains( $bootstrap, $presentation_only_contract ) ) {
		$failures[] = 'Admin presentation contract must not be a hard Dictionary boot dependency: ' . $presentation_only_contract . '.';
	}
}
if ( ! str_contains( $bootstrap, '\\CB\\Dictionary\\Support\\Requirements::runtime_ready()' ) ) {
	$failures[] = 'Dictionary bootstrap must delegate runtime dependency validation to Support\\Requirements.';
}
$requirements = (string) file_get_contents( $root . '/src/Support/Requirements.php' );
if ( ! str_contains( $requirements, "'\\\\CB\\\\Core\\\\Admin\\\\SettingsRegistry'" ) ) {
	$failures[] = 'Dictionary requirements must include the public SettingsRegistry contract.';
}
foreach ( [ '\\CB\\Core\\Admin\\PageRegistry', '\\CB\\Core\\Admin\\Page' ] as $legacy_boot_contract ) {
	if ( str_contains( $bootstrap, $legacy_boot_contract ) ) {
		$failures[] = 'Dictionary bootstrap must not require the retired flat settings contract: ' . $legacy_boot_contract . '.';
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

$element_registry = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/ElementRegistry.php' );
foreach ( [ 'cb-dictionary-search', 'cb-dictionary-search-results', 'cb-dictionary-alphabet', 'cb-dictionary-entries', 'cb-dictionary-entry-data' ] as $element_name ) {
	if ( ! str_contains( $element_registry, $element_name ) ) {
		$failures[] = 'Dictionary Bricks element registry is missing ' . $element_name . '.';
	}
}

foreach ( [
	'Search.php'        => 'SearchComponent::render',
	'SearchResults.php' => 'SearchResultsComponent::render',
	'Alphabet.php'      => 'AlphabetComponent::render',
	'Entries.php'       => 'EntriesComponent::render',
	'EntryData.php'     => 'MetaComponent::render',
] as $element_file => $required_delegate ) {
	$content = (string) file_get_contents( $root . '/src/Integration/Builders/Bricks/Elements/' . $element_file );
	if ( ! str_contains( $content, $required_delegate ) ) {
		$failures[] = 'Dictionary Bricks element must delegate to builder-neutral frontend component: ' . $element_file . '.';
	}
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
