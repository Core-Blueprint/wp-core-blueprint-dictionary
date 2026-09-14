<?php
/**
 * Plugin Name:       Core Blueprint Dictionary
 * Plugin URI:        https://coreblueprint.io
 * Description:       Lightweight builder-agnostic digital dictionary with native WordPress content and taxonomies.
 * Version:           1.0.0-rc1
 * Author:            Core Blueprint
 * Author URI:        https://coreblueprint.io
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       core-blueprint-dictionary
 * Domain Path:       /languages
 * Requires at least: 7.0
 * Requires PHP:      8.4
 * Requires Plugins: core-blueprint
 *
 * @package CB_Dictionary
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

if ( version_compare( PHP_VERSION, '8.4', '<' ) ) {
	register_activation_hook( __FILE__, static function (): void {
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die(
			esc_html( sprintf( 'PHP %1$s or newer is required. This server runs PHP %2$s.', '8.4', PHP_VERSION ) ),
			esc_html( 'Core Blueprint requirements not met' ),
			[
				'link_url'  => admin_url( 'plugins.php' ),
				'link_text' => 'Plugins',
			]
		);
	} );

	add_action( 'admin_notices', static function (): void {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
				esc_html( 'Core Blueprint Dictionary:' ),
				esc_html( sprintf( 'PHP %1$s or newer is required. This server runs PHP %2$s.', '8.4', PHP_VERSION ) )
			);
		}
	} );
	return;
}

define( 'CB_DICTIONARY_VERSION',      '1.0.0-rc1' );
define( 'CB_DICTIONARY_REQUIRED_API', '1.0' );
define( 'CB_DICTIONARY_FILE',         __FILE__ );
define( 'CB_DICTIONARY_DIR',          plugin_dir_path( __FILE__ ) );
define( 'CB_DICTIONARY_URL',          plugin_dir_url( __FILE__ ) );
define( 'CB_DICTIONARY_BASENAME',     plugin_basename( __FILE__ ) );

spl_autoload_register( static function ( string $class ): void {
	$prefix = 'CB\\Dictionary\\';
	$length = strlen( $prefix );

	if ( 0 !== strncmp( $class, $prefix, $length ) ) {
		return;
	}

	$relative = substr( $class, $length );
	$file     = CB_DICTIONARY_DIR . 'src/' . str_replace( '\\', '/', $relative ) . '.php';

	if ( is_file( $file ) ) {
		require_once $file;
	}
} );

add_action( 'init', static function (): void {
	load_plugin_textdomain(
		'core-blueprint-dictionary',
		false,
		dirname( CB_DICTIONARY_BASENAME ) . '/languages'
	);
}, 1 );

function cb_dictionary_fail_activation( string $message ): void {
	if ( ! function_exists( 'deactivate_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	deactivate_plugins( CB_DICTIONARY_BASENAME );
	wp_die(
		esc_html( $message ),
		esc_html( 'Core Blueprint requirements not met' ),
		[
			'link_url'  => admin_url( 'plugins.php' ),
			'link_text' => 'Plugins',
		]
	);
}

function cb_dictionary_activate(): void {
	if ( ! \CB\Dictionary\Support\Requirements::runtime_ready() ) {
		cb_dictionary_fail_activation( \CB\Dictionary\Support\Requirements::activation_message() );
	}
	if ( ! \CB\Dictionary\Support\Requirements::product_ready() ) {
		cb_dictionary_fail_activation( \CB\Dictionary\Support\Requirements::product_activation_message() );
	}

	\CB\Dictionary\Install::activate();
}
register_activation_hook( __FILE__, 'cb_dictionary_activate' );
register_deactivation_hook( __FILE__, [ '\\CB\\Dictionary\\Install', 'deactivate' ] );

add_action( 'plugins_loaded', static function (): void {
	if ( ! \CB\Dictionary\Support\Requirements::runtime_ready() ) {
		if ( is_admin() ) {
			add_action( 'admin_notices', static function (): void {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				printf(
					'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
					esc_html__( 'Core Blueprint Dictionary:', 'core-blueprint-dictionary' ),
					esc_html( \CB\Dictionary\Support\Requirements::operator_message() )
				);
			} );
		}
		return;
	}

	/* Lightweight Suite identity attaches after generic Bootstrap readiness. */
	\CB\Dictionary\Integration\Suite::init();

	if ( ! \CB\Dictionary\Support\Requirements::product_ready() ) {
		if ( is_admin() ) {
			add_action( 'admin_notices', static function (): void {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}
				printf(
					'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
					esc_html__( 'Core Blueprint Dictionary:', 'core-blueprint-dictionary' ),
					esc_html( \CB\Dictionary\Support\Requirements::product_operator_message() )
				);
			} );
		}
		return;
	}

	\CB\Dictionary\Plugin::boot();
}, 30 );
