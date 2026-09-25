<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks\Elements;

use CB\Dictionary\Frontend\Components\Alphabet as AlphabetComponent;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class Alphabet extends \Bricks\Element {
	public $category = ElementRegistry::CATEGORY;
	public $name     = 'cb-dictionary-alphabet';
	public $icon     = 'ti-layout-grid2';

	public function get_label(): string {
		return esc_html__( 'Dictionary Alphabet', 'core-blueprint-dictionary' );
	}

	/** @return string[] */
	public function get_keywords(): array {
		return [ 'core blueprint', 'dictionary', 'alphabet', 'a-z', 'glossary' ];
	}

	public function set_control_groups(): void {
		$this->control_groups['alphabet'] = [
			'title' => esc_html__( 'Alphabet', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['layout'] = [
			'title' => esc_html__( 'Layout', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['letters'] = [
			'title' => esc_html__( 'Letters', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['current'] = [
			'title' => esc_html__( 'Current letter', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['empty'] = [
			'title' => esc_html__( 'Empty letters', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
	}

	public function set_controls(): void {
		$this->controls['showEmpty'] = [
			'tab'     => 'content',
			'group'   => 'alphabet',
			'label'   => esc_html__( 'Show empty letters', 'core-blueprint-dictionary' ),
			'type'    => 'checkbox',
			'default' => false,
		];
		$this->controls['listDisplay'] = [
			'tab'     => 'content',
			'group'   => 'layout',
			'label'   => esc_html__( 'Display', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				'flex' => 'flex',
				'grid' => 'grid',
			],
			'css'     => [ [ 'property' => 'display', 'selector' => '.cb-dictionary-alphabet__items' ] ],
		];
		$this->controls['listStyleType'] = [
			'tab'     => 'content',
			'group'   => 'layout',
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
			'css'     => [ [ 'property' => 'list-style-type', 'selector' => '.cb-dictionary-alphabet__items' ] ],
		];
		$this->controls['listMargin'] = [
			'tab'   => 'content',
			'group' => 'layout',
			'label' => esc_html__( 'List margin', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'margin', 'selector' => '.cb-dictionary-alphabet__items' ] ],
		];
		$this->controls['listPadding'] = [
			'tab'   => 'content',
			'group' => 'layout',
			'label' => esc_html__( 'List padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-alphabet__items' ] ],
		];

		$this->controls['listColumns'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Grid columns', 'core-blueprint-dictionary' ),
			'type'     => 'select',
			'options'  => [
				'repeat(4, minmax(0, 1fr))'  => '4',
				'repeat(6, minmax(0, 1fr))'  => '6',
				'repeat(8, minmax(0, 1fr))'  => '8',
				'repeat(13, minmax(0, 1fr))' => '13',
			],
			'css'      => [ [ 'property' => 'grid-template-columns', 'selector' => '.cb-dictionary-alphabet__items' ] ],
			'required' => [ 'listDisplay', '=', 'grid' ],
		];

		$this->controls['listFlexWrap'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Flex wrap', 'core-blueprint-dictionary' ),
			'type'     => 'select',
			'options'  => [
				'nowrap'       => esc_html__( 'No wrap', 'core-blueprint-dictionary' ),
				'wrap'         => esc_html__( 'Wrap', 'core-blueprint-dictionary' ),
				'wrap-reverse' => esc_html__( 'Wrap reverse', 'core-blueprint-dictionary' ),
			],
			'inline'   => true,
			'css'      => [ [ 'property' => 'flex-wrap', 'selector' => '.cb-dictionary-alphabet__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		$this->controls['listDirection'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Direction', 'core-blueprint-dictionary' ),
			'type'     => 'direction',
			'inline'   => true,
			'rerender' => true,
			'css'      => [ [ 'property' => 'flex-direction', 'selector' => '.cb-dictionary-alphabet__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		$this->controls['listJustifyContent'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Align main axis', 'core-blueprint-dictionary' ),
			'type'     => 'justify-content',
			'css'      => [ [ 'property' => 'justify-content', 'selector' => '.cb-dictionary-alphabet__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		$this->controls['listAlignItems'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Align cross axis', 'core-blueprint-dictionary' ),
			'type'     => 'align-items',
			'css'      => [ [ 'property' => 'align-items', 'selector' => '.cb-dictionary-alphabet__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		$this->controls['listColumnGap'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Column gap', 'core-blueprint-dictionary' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [ [ 'property' => 'column-gap', 'selector' => '.cb-dictionary-alphabet__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		$this->controls['listRowGap'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Row gap', 'core-blueprint-dictionary' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [ [ 'property' => 'row-gap', 'selector' => '.cb-dictionary-alphabet__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];

		$this->controls['listGridGap'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Grid gap', 'core-blueprint-dictionary' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [ [ 'property' => 'gap', 'selector' => '.cb-dictionary-alphabet__items' ] ],
			'required' => [ 'listDisplay', '=', 'grid' ],
		];


		$this->controls['typography'] = [
			'tab'   => 'content',
			'group' => 'letters',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [
				[ 'property' => 'typography', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'typography', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
		$this->controls['background'] = [
			'tab'   => 'content',
			'group' => 'letters',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'exclude' => [ 'videoUrl', 'videoScale' ],
			'css'   => [
				[ 'property' => 'background', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'background', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
		$this->controls['border'] = [
			'tab'   => 'content',
			'group' => 'letters',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [
				[ 'property' => 'border', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'border', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
		$this->controls['padding'] = [
			'tab'   => 'content',
			'group' => 'letters',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [
				[ 'property' => 'padding', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'padding', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
		$this->controls['shadow'] = [
			'tab'   => 'content',
			'group' => 'letters',
			'label' => esc_html__( 'Box shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [
				[ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
		$this->controls['hoverColor'] = [
			'tab'   => 'content',
			'group' => 'letters',
			'label' => esc_html__( 'Hover text color', 'core-blueprint-dictionary' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.cb-dictionary-alphabet__link:hover' ] ],
		];
		$this->controls['hoverBackground'] = [
			'tab'   => 'content',
			'group' => 'letters',
			'label' => esc_html__( 'Hover background color', 'core-blueprint-dictionary' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'background-color', 'selector' => '.cb-dictionary-alphabet__link:hover' ] ],
		];

		$this->controls['currentTypography'] = [
			'tab'   => 'content',
			'group' => 'current',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-alphabet__item--current .cb-dictionary-alphabet__link' ] ],
		];
		$this->controls['currentBackground'] = [
			'tab'   => 'content',
			'group' => 'current',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'exclude' => [ 'videoUrl', 'videoScale' ],
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-alphabet__item--current .cb-dictionary-alphabet__link' ] ],
		];
		$this->controls['currentBorder'] = [
			'tab'   => 'content',
			'group' => 'current',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-alphabet__item--current .cb-dictionary-alphabet__link' ] ],
		];

		$this->controls['emptyTypography'] = [
			'tab'   => 'content',
			'group' => 'empty',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-alphabet__label--empty' ] ],
			'required' => [ 'showEmpty', '=', true ],
		];
		$this->controls['emptyBackground'] = [
			'tab'   => 'content',
			'group' => 'empty',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'exclude' => [ 'videoUrl', 'videoScale' ],
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-alphabet__label--empty' ] ],
			'required' => [ 'showEmpty', '=', true ],
		];
		$this->controls['emptyBorder'] = [
			'tab'   => 'content',
			'group' => 'empty',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-alphabet__label--empty' ] ],
			'required' => [ 'showEmpty', '=', true ],
		];
		$this->controls['emptyOpacity'] = [
			'tab'   => 'content',
			'group' => 'empty',
			'label' => esc_html__( 'Opacity', 'core-blueprint-dictionary' ),
			'type'  => 'number',
			'min'   => 0,
			'max'   => 1,
			'step'  => 0.1,
			'css'   => [ [ 'property' => 'opacity', 'selector' => '.cb-dictionary-alphabet__label--empty' ] ],
			'required' => [ 'showEmpty', '=', true ],
		];
	}

	public function render(): void {
		$settings = $this->settings;
		$args = [
			'show_empty' => array_key_exists( 'showEmpty', $settings ) ? (bool) $settings['showEmpty'] : false,
		];

		$this->set_attribute( '_root', 'class', 'cb-dictionary-bricks-alphabet' );
		echo '<div ' . $this->render_attributes( '_root' ) . '>' . AlphabetComponent::render( $args ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted builder-neutral Dictionary renderer output.
	}
}
