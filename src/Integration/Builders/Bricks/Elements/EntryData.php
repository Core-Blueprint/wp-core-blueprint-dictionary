<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks\Elements;

use CB\Dictionary\Frontend\Components\Meta as MetaComponent;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class EntryData extends \Bricks\Element {
	public $category = ElementRegistry::CATEGORY;
	public $name     = 'cb-dictionary-entry-data';
	public $icon     = 'ti-info-alt';

	public function get_label(): string {
		return esc_html__( 'Dictionary Entry Data', 'core-blueprint-dictionary' );
	}

	/** @return string[] */
	public function get_keywords(): array {
		return [ 'core blueprint', 'dictionary', 'meta', 'entry', 'data' ];
	}

	public function set_control_groups(): void {
		$this->control_groups['entry'] = [
			'title' => esc_html__( 'Entry', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['layout'] = [
			'title' => esc_html__( 'Layout', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
		$this->control_groups['items'] = [
			'title' => esc_html__( 'Rows', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
		$this->control_groups['labels'] = [
			'title' => esc_html__( 'Labels', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
		$this->control_groups['values'] = [
			'title' => esc_html__( 'Values', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
	}

	public function set_controls(): void {
		$this->controls['entryId'] = [
			'tab'         => 'content',
			'group'       => 'entry',
			'label'       => esc_html__( 'Entry ID', 'core-blueprint-dictionary' ),
			'type'        => 'number',
			'default'     => 0,
			'min'         => 0,
			'description' => esc_html__( 'Leave at 0 to use the current Dictionary entry context.', 'core-blueprint-dictionary' ),
		];
		$this->controls['metaDisplay'] = [
			'tab'     => 'style',
			'group'   => 'layout',
			'label'   => esc_html__( 'Display', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				'grid'  => 'grid',
				'flex'  => 'flex',
				'block' => 'block',
			],
			'css'     => [ [ 'property' => 'display', 'selector' => '.cb-dictionary-meta' ] ],
		];
		$this->controls['metaColumns'] = [
			'tab'      => 'style',
			'group'    => 'layout',
			'label'    => esc_html__( 'Grid columns', 'core-blueprint-dictionary' ),
			'type'     => 'select',
			'options'  => [
				'1fr'                       => '1',
				'repeat(2, minmax(0, 1fr))' => '2',
				'repeat(3, minmax(0, 1fr))' => '3',
			],
			'css'      => [ [ 'property' => 'grid-template-columns', 'selector' => '.cb-dictionary-meta' ] ],
			'required' => [ 'metaDisplay', '=', 'grid' ],
		];
		$this->controls['metaGap'] = [
			'tab'   => 'style',
			'group' => 'layout',
			'label' => esc_html__( 'Gap', 'core-blueprint-dictionary' ),
			'type'  => 'slider',
			'css'   => [ [ 'property' => 'gap', 'selector' => '.cb-dictionary-meta' ] ],
		];

		$this->controls['rowDisplay'] = [
			'tab'     => 'style',
			'group'   => 'items',
			'label'   => esc_html__( 'Row display', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				'grid'  => 'grid',
				'flex'  => 'flex',
				'block' => 'block',
			],
			'css'     => [ [ 'property' => 'display', 'selector' => '.cb-dictionary-meta__item' ] ],
		];
		$this->controls['rowGap'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Row gap', 'core-blueprint-dictionary' ),
			'type'  => 'slider',
			'css'   => [ [ 'property' => 'gap', 'selector' => '.cb-dictionary-meta__item' ] ],
		];
		$this->controls['rowAlignItems'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Align items', 'core-blueprint-dictionary' ),
			'type'  => 'align-items',
			'css'   => [ [ 'property' => 'align-items', 'selector' => '.cb-dictionary-meta__item' ] ],
		];
		$this->controls['rowBackground'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-meta__item' ] ],
		];
		$this->controls['rowBorder'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-meta__item' ] ],
		];
		$this->controls['rowPadding'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-meta__item' ] ],
		];
		$this->controls['rowShadow'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Box shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-meta__item' ] ],
		];

		$this->controls['labelTypography'] = [
			'tab'   => 'style',
			'group' => 'labels',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-meta__label' ] ],
		];
		$this->controls['labelBackground'] = [
			'tab'   => 'style',
			'group' => 'labels',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-meta__label' ] ],
		];
		$this->controls['labelPadding'] = [
			'tab'   => 'style',
			'group' => 'labels',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-meta__label' ] ],
		];
		$this->controls['labelWidth'] = [
			'tab'   => 'style',
			'group' => 'labels',
			'label' => esc_html__( 'Width', 'core-blueprint-dictionary' ),
			'type'  => 'slider',
			'css'   => [ [ 'property' => 'width', 'selector' => '.cb-dictionary-meta__label' ] ],
		];

		$this->controls['valueTypography'] = [
			'tab'   => 'style',
			'group' => 'values',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-meta__value' ] ],
		];
		$this->controls['valueBackground'] = [
			'tab'   => 'style',
			'group' => 'values',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-meta__value' ] ],
		];
		$this->controls['valuePadding'] = [
			'tab'   => 'style',
			'group' => 'values',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-meta__value' ] ],
		];
	}

	public function render(): void {
		$settings = $this->settings;
		$this->set_attribute( '_root', 'class', 'cb-dictionary-bricks-entry-data' );
		echo '<div ' . $this->render_attributes( '_root' ) . '>' . MetaComponent::render( [ 'id' => $settings['entryId'] ?? 0 ] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted builder-neutral Dictionary renderer output.
	}
}
