<?php
declare(strict_types=1);

namespace CB\Dictionary\Admin;

use CB\Core\Admin\Page as PageContract;
use CB\Core\Admin\PageRegistry;
use CB\Core\UI\Notice;
use CB\Dictionary\Settings;

defined( 'ABSPATH' ) || exit;

final class SettingsPage implements PageContract {
	public const SLUG = 'core-blueprint-dictionary-settings';

	public static function init(): void {
		add_action( 'cb_core_register_pages', [ __CLASS__, 'register' ] );
	}

	public static function register(): void {
		PageRegistry::register(
			new self(),
			[ 'components' => [ 'panels', 'notices', 'form-controls' ] ]
		);
	}

	public function slug(): string { return self::SLUG; }
	public function title(): string { return __( 'Dictionary Settings', 'core-blueprint-dictionary' ); }
	public function menu_title(): string { return __( 'Dictionary', 'core-blueprint-dictionary' ); }
	public function capability(): string { return 'manage_options'; }
	public function position(): ?int { return null; }

	public function render(): void {
		if ( ! current_user_can( $this->capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'core-blueprint-dictionary' ) );
		}

		$rewrite_base = Settings::rewrite_base();
		$example      = home_url( '/' . $rewrite_base . '/example-term/' );
		$updated      = isset( $_GET['cb_dictionary_updated'] )
			? sanitize_key( (string) wp_unslash( $_GET['cb_dictionary_updated'] ) )
			: ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only redirect state.
		?>
		<div class="wrap cb-core-wrap cb-dictionary-settings-wrap">
			<h1 class="cb-core-title"><?php esc_html_e( 'Core Blueprint Dictionary', 'core-blueprint-dictionary' ); ?></h1>
			<p class="cb-core-intro"><?php esc_html_e( 'Configure the small set of site-wide Dictionary settings. Entries and presentation remain native WordPress and builder-agnostic.', 'core-blueprint-dictionary' ); ?></p>

			<?php if ( 'changed' === $updated ) : ?>
				<?php echo Notice::render( [ 'variant' => Notice::SUCCESS, 'title' => __( 'URL base updated', 'core-blueprint-dictionary' ), 'message' => __( 'The new Dictionary URL base is active. WordPress rewrite rules were refreshed once after the new routes were registered.', 'core-blueprint-dictionary' ) ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php elseif ( 'unchanged' === $updated ) : ?>
				<?php echo Notice::render( [ 'variant' => Notice::INFO, 'title' => __( 'No changes needed', 'core-blueprint-dictionary' ), 'message' => __( 'The Dictionary URL base already had this value, so no rewrite refresh was necessary.', 'core-blueprint-dictionary' ) ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>

			<section class="cb-core-panel">
				<h2><?php esc_html_e( 'Permalinks', 'core-blueprint-dictionary' ); ?></h2>
				<p><?php esc_html_e( 'Choose the URL base used by the Dictionary archive, individual entries and Dictionary taxonomy archives. Changing this later changes public URLs, so existing external links may need redirects.', 'core-blueprint-dictionary' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="cb_dictionary_save_settings">
					<?php wp_nonce_field( 'cb_dictionary_save_settings', 'cb_dictionary_settings_nonce' ); ?>
					<p><label for="cb_dictionary_rewrite_base"><strong><?php esc_html_e( 'Dictionary URL base', 'core-blueprint-dictionary' ); ?></strong></label><br><input class="regular-text" type="text" id="cb_dictionary_rewrite_base" name="rewrite_base" value="<?php echo esc_attr( $rewrite_base ); ?>" placeholder="dictionary" autocomplete="off"></p>
					<p class="description"><?php esc_html_e( 'Use a slug such as dictionary, glossary, woordenboek or begrippen. Nested paths such as academy/dictionary are also supported. Empty or invalid input falls back to dictionary.', 'core-blueprint-dictionary' ); ?></p>
					<p class="description"><strong><?php esc_html_e( 'Example:', 'core-blueprint-dictionary' ); ?></strong> <code><?php echo esc_html( $example ); ?></code></p>
					<?php submit_button( __( 'Save URL base', 'core-blueprint-dictionary' ) ); ?>
				</form>
			</section>

			<section class="cb-core-panel">
				<h2><?php esc_html_e( 'Alphabet', 'core-blueprint-dictionary' ); ?></h2>
				<p><?php esc_html_e( 'Dictionary maintains a fixed 0-9 and A-Z taxonomy automatically. Users manage Categories and Tags themselves; the Alphabet taxonomy is machine-owned and is assigned from each entry title.', 'core-blueprint-dictionary' ); ?></p>
			</section>
		</div>
		<?php
	}
}
