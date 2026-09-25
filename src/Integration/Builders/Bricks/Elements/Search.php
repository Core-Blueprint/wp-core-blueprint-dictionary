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
		$this->control_groups['form'] = [
			'title' => esc_html__( 'Form layout', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['input'] = [
			'title' => esc_html__( 'Input', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['button'] = [
			'title' => esc_html__( 'Button', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['results'] = [
			'title' => esc_html__( 'Inline results', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['resultItems'] = [
			'title' => esc_html__( 'Result items', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['status'] = [
			'title' => esc_html__( 'Count & status', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
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

		$this->controls['formDisplay'] = [
			'tab'     => 'content',
			'group'   => 'form',
			'label'   => esc_html__( 'Display', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				'flex'  => 'flex',
				'grid'  => 'grid',
				'block' => 'block',
			],
			'css'     => [ [ 'property' => 'display', 'selector' => '.cb-dictionary-search__form' ] ],
		];
		$this->controls['formGap'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Gap', 'core-blueprint-dictionary' ),
			'type'  => 'slider',
			'css'   => [ [ 'property' => 'gap', 'selector' => '.cb-dictionary-search__form' ] ],
		];
		$this->controls['formAlignItems'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Align items', 'core-blueprint-dictionary' ),
			'type'  => 'align-items',
			'css'   => [ [ 'property' => 'align-items', 'selector' => '.cb-dictionary-search__form' ] ],
		];
		$this->controls['formJustifyContent'] = [
			'tab'   => 'content',
			'group' => 'form',
			'label' => esc_html__( 'Justify content', 'core-blueprint-dictionary' ),
			'type'  => 'justify-content',
			'css'   => [ [ 'property' => 'justify-content', 'selector' => '.cb-dictionary-search__form' ] ],
		];

		$this->controls['inputTypography'] = [
			'tab'   => 'content',
			'group' => 'input',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputBackground'] = [
			'tab'   => 'content',
			'group' => 'input',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputBorder'] = [
			'tab'   => 'content',
			'group' => 'input',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputPadding'] = [
			'tab'   => 'content',
			'group' => 'input',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputShadow'] = [
			'tab'   => 'content',
			'group' => 'input',
			'label' => esc_html__( 'Box shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputMinHeight'] = [
			'tab'   => 'content',
			'group' => 'input',
			'label' => esc_html__( 'Minimum height', 'core-blueprint-dictionary' ),
			'type'  => 'slider',
			'css'   => [ [ 'property' => 'min-height', 'selector' => '.cb-dictionary-search__input' ] ],
		];
		$this->controls['inputFocusBorder'] = [
			'tab'   => 'content',
			'group' => 'input',
			'label' => esc_html__( 'Focus border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search__input:focus' ] ],
		];
		$this->controls['inputFocusShadow'] = [
			'tab'   => 'content',
			'group' => 'input',
			'label' => esc_html__( 'Focus shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-search__input:focus' ] ],
		];

		$this->controls['buttonTypography'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonBackground'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonBorder'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonPadding'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonShadow'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Box shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonMinHeight'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Minimum height', 'core-blueprint-dictionary' ),
			'type'  => 'slider',
			'css'   => [ [ 'property' => 'min-height', 'selector' => '.cb-dictionary-search__submit' ] ],
		];
		$this->controls['buttonHoverColor'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Hover text color', 'core-blueprint-dictionary' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.cb-dictionary-search__submit:hover' ] ],
		];
		$this->controls['buttonHoverBackground'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Hover background color', 'core-blueprint-dictionary' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'background-color', 'selector' => '.cb-dictionary-search__submit:hover' ] ],
		];
		$this->controls['buttonHoverBorder'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Hover border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search__submit:hover' ] ],
		];

		$this->controls['resultsBackground'] = [
			'tab'   => 'content',
			'group' => 'results',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-search-results' ] ],
		];
		$this->controls['resultsBorder'] = [
			'tab'   => 'content',
			'group' => 'results',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search-results' ] ],
		];
		$this->controls['resultsPadding'] = [
			'tab'   => 'content',
			'group' => 'results',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-search-results' ] ],
		];
		$this->controls['resultsShadow'] = [
			'tab'   => 'content',
			'group' => 'results',
			'label' => esc_html__( 'Box shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-search-results' ] ],
		];
		$this->controls['resultsListDisplay'] = [
			'tab'     => 'content',
			'group'   => 'results',
			'label'   => esc_html__( 'List display', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				'grid'  => 'grid',
				'flex'  => 'flex',
				'block' => 'block',
			],
			'css'     => [ [ 'property' => 'display', 'selector' => '.cb-dictionary-search-results__items' ] ],
		];
		$this->controls['resultsListStyleType'] = [
			'tab'     => 'content',
			'group'   => 'results',
			'label'   => esc_html__( 'List marker', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				''                     => esc_html__( 'Browser default', 'core-blueprint-dictionary' ),
				'none'                 => esc_html__( 'None', 'core-blueprint-dictionary' ),
				'disc'                 => esc_html__( 'Disc', 'core-blueprint-dictionary' ),
				'circle'               => esc_html__( 'Circle', 'core-blueprint-dictionary' ),
				'square'               => esc_html__( 'Square', 'core-blueprint-dictionary' ),
				'decimal'              => esc_html__( 'Decimal', 'core-blueprint-dictionary' ),
				'decimal-leading-zero' => esc_html__( 'Decimal leading zero', 'core-blueprint-dictionary' ),
				'lower-alpha'          => esc_html__( 'Lower alpha', 'core-blueprint-dictionary' ),
				'upper-alpha'          => esc_html__( 'Upper alpha', 'core-blueprint-dictionary' ),
				'lower-roman'          => esc_html__( 'Lower roman', 'core-blueprint-dictionary' ),
				'upper-roman'          => esc_html__( 'Upper roman', 'core-blueprint-dictionary' ),
			],
			'default' => '',
			'css'     => [ [ 'property' => 'list-style-type', 'selector' => '.cb-dictionary-search-results__items' ] ],
		];
		$this->controls['resultsColumns'] = [
			'tab'      => 'content',
			'group'    => 'results',
			'label'    => esc_html__( 'Grid columns', 'core-blueprint-dictionary' ),
			'type'     => 'select',
			'options'  => [
				'1fr'                         => '1',
				'repeat(2, minmax(0, 1fr))'   => '2',
				'repeat(3, minmax(0, 1fr))'   => '3',
				'repeat(4, minmax(0, 1fr))'   => '4',
			],
			'css'      => [ [ 'property' => 'grid-template-columns', 'selector' => '.cb-dictionary-search-results__items' ] ],
			'required' => [ 'resultsListDisplay', '=', 'grid' ],
		];
		$this->controls['resultsGap'] = [
			'tab'   => 'content',
			'group' => 'results',
			'label' => esc_html__( 'List gap', 'core-blueprint-dictionary' ),
			'type'  => 'slider',
			'css'   => [ [ 'property' => 'gap', 'selector' => '.cb-dictionary-search-results__items' ] ],
		];

		$this->controls['resultItemBackground'] = [
			'tab'   => 'content',
			'group' => 'resultItems',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-search-results__item' ] ],
		];
		$this->controls['resultItemBorder'] = [
			'tab'   => 'content',
			'group' => 'resultItems',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search-results__item' ] ],
		];
		$this->controls['resultItemPadding'] = [
			'tab'   => 'content',
			'group' => 'resultItems',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-search-results__item' ] ],
		];
		$this->controls['resultItemShadow'] = [
			'tab'   => 'content',
			'group' => 'resultItems',
			'label' => esc_html__( 'Box shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-search-results__item' ] ],
		];
		$this->controls['resultItemHoverBackground'] = [
			'tab'   => 'content',
			'group' => 'resultItems',
			'label' => esc_html__( 'Hover background color', 'core-blueprint-dictionary' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'background-color', 'selector' => '.cb-dictionary-search-results__item:hover' ] ],
		];

		$this->controls['resultsTitleTypography'] = [
			'tab'   => 'content',
			'group' => 'resultItems',
			'label' => esc_html__( 'Title typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search-results__title' ] ],
		];
		$this->controls['resultsExcerptTypography'] = [
			'tab'   => 'content',
			'group' => 'resultItems',
			'label' => esc_html__( 'Excerpt typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search-results__excerpt' ] ],
		];
		$this->controls['resultTitleHoverColor'] = [
			'tab'   => 'content',
			'group' => 'resultItems',
			'label' => esc_html__( 'Title hover color', 'core-blueprint-dictionary' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.cb-dictionary-search-results__link:hover .cb-dictionary-search-results__title' ] ],
		];
		$this->controls['countTypography'] = [
			'tab'   => 'content',
			'group' => 'status',
			'label' => esc_html__( 'Result count typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search-results__count' ] ],
		];
		$this->controls['statusTypography'] = [
			'tab'   => 'content',
			'group' => 'status',
			'label' => esc_html__( 'Status typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search-results__status' ] ],
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
