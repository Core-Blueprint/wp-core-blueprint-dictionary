<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks\Elements;

use CB\Dictionary\Frontend\Components\Search as SearchComponent;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class Search extends \Bricks\Element {
	public $category = ElementRegistry::CATEGORY;
	public $name     = 'cb-dictionary-search';
	public $icon     = 'ti-search';

	public function get_label(): string {
		return esc_html__( 'Dictionary Search', 'core-blueprint-dictionary' );
	}

	/** @return string[] */
	public function get_keywords(): array {
		return [ 'core blueprint', 'dictionary', 'glossary', 'search' ];
	}

	public function set_control_groups(): void {
		$this->control_groups['search'] = [
			'title' => esc_html__( 'Search', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['input'] = [
			'title' => esc_html__( 'Input', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
		$this->control_groups['button'] = [
			'title' => esc_html__( 'Button', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
		$this->control_groups['results'] = [
			'title' => esc_html__( 'Inline results', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
	}

	public function set_controls(): void {
		$this->controls['usage'] = [
			'tab'     => 'content',
			'group'   => 'search',
			'type'    => 'info',
			'content' => esc_html__( 'Use inline results, or choose external results and pair this element with Dictionary Search Results using the same source key.', 'core-blueprint-dictionary' ),
		];
		$this->controls['placeholder'] = [
			'tab'     => 'content',
			'group'   => 'search',
			'label'   => esc_html__( 'Placeholder', 'core-blueprint-dictionary' ),
			'type'    => 'text',
			'default' => esc_html__( 'Search dictionary…', 'core-blueprint-dictionary' ),
		];
		$this->controls['resultsMode'] = [
			'tab'     => 'content',
			'group'   => 'search',
			'label'   => esc_html__( 'Results placement', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				'inline'   => esc_html__( 'Inline', 'core-blueprint-dictionary' ),
				'external' => esc_html__( 'External results element', 'core-blueprint-dictionary' ),
			],
			'default' => 'inline',
		];
		$this->controls['sourceKey'] = [
			'tab'         => 'content',
			'group'       => 'search',
			'label'       => esc_html__( 'Source key', 'core-blueprint-dictionary' ),
			'type'        => 'text',
			'default'     => 'default',
			'description' => esc_html__( 'Search and Search Results elements are paired by this key.', 'core-blueprint-dictionary' ),
		];
		$this->controls['limit'] = [
			'tab'     => 'content',
			'group'   => 'search',
			'label'   => esc_html__( 'Result limit', 'core-blueprint-dictionary' ),
			'type'    => 'number',
			'default' => 30,
			'min'     => 1,
			'max'     => 100,
			'step'    => 1,
		];
		$this->controls['showExcerpt'] = [
			'tab'     => 'content',
			'group'   => 'search',
			'label'   => esc_html__( 'Show excerpts', 'core-blueprint-dictionary' ),
			'type'    => 'checkbox',
			'default' => false,
		];
		$this->controls['showCount'] = [
			'tab'     => 'content',
			'group'   => 'search',
			'label'   => esc_html__( 'Show result count', 'core-blueprint-dictionary' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['inputTypography'] = [
			'tab'   => 'style',
			'group' => 'input',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputBackground'] = [
			'tab'   => 'style',
			'group' => 'input',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputBorder'] = [
			'tab'   => 'style',
			'group' => 'input',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputPadding'] = [
			'tab'   => 'style',
			'group' => 'input',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-search__input' ] ],
		];

		$this->controls['buttonTypography'] = [
			'tab'   => 'style',
			'group' => 'button',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonBackground'] = [
			'tab'   => 'style',
			'group' => 'button',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonBorder'] = [
			'tab'   => 'style',
			'group' => 'button',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonPadding'] = [
			'tab'   => 'style',
			'group' => 'button',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-search__submit' ] ],
		];

		$this->controls['resultsTitleTypography'] = [
			'tab'   => 'style',
			'group' => 'results',
			'label' => esc_html__( 'Title typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search-results__title' ] ],
		];
		$this->controls['resultsExcerptTypography'] = [
			'tab'   => 'style',
			'group' => 'results',
			'label' => esc_html__( 'Excerpt typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search-results__excerpt' ] ],
		];
	}

	public function render(): void {
		$settings = $this->settings;
		$args = [
			'placeholder' => (string) ( $settings['placeholder'] ?? __( 'Search dictionary…', 'core-blueprint-dictionary' ) ),
			'source'      => (string) ( $settings['sourceKey'] ?? 'default' ),
			'results'     => (string) ( $settings['resultsMode'] ?? 'inline' ),
			'limit'       => $settings['limit'] ?? 30,
			'excerpt'     => self::checkbox( $settings, 'showExcerpt', false ),
			'show_count'  => self::checkbox( $settings, 'showCount', false ),
		];

		$this->set_attribute( '_root', 'class', 'cb-dictionary-bricks-search' );
		echo '<div ' . $this->render_attributes( '_root' ) . '>' . SearchComponent::render( $args ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted builder-neutral Dictionary renderer output.
	}

	/** @param array<string,mixed> $settings */
	private static function checkbox( array $settings, string $key, bool $default ): bool {
		if ( ! array_key_exists( $key, $settings ) ) {
			return $default;
		}
		return (bool) $settings[ $key ];
	}
}
