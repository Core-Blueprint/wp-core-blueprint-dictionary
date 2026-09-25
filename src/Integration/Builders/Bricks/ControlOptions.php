<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks;

defined( 'ABSPATH' ) || exit;

/**
 * Shared Bricks control configuration for Dictionary-specific elements.
 */
final class ControlOptions {
	/** @return array<string,array{min:float|int,max:float|int,step:float|int}> */
	public static function spacing_units(): array {
		return [
			'px' => [
				'min'  => 0,
				'max'  => 400,
				'step' => 1,
			],
			'rem' => [
				'min'  => 0,
				'max'  => 25,
				'step' => 0.1,
			],
			'em' => [
				'min'  => 0,
				'max'  => 25,
				'step' => 0.1,
			],
		];
	}

	/** @return array<string,array{min:float|int,max:float|int,step:float|int}> */
	public static function size_units(): array {
		return [
			'px' => [
				'min'  => 0,
				'max'  => 600,
				'step' => 1,
			],
			'rem' => [
				'min'  => 0,
				'max'  => 40,
				'step' => 0.1,
			],
			'em' => [
				'min'  => 0,
				'max'  => 40,
				'step' => 0.1,
			],
		];
	}

	/** @return array<string,array{min:float|int,max:float|int,step:float|int}> */
	public static function icon_units(): array {
		return [
			'px' => [
				'min'  => 1,
				'max'  => 200,
				'step' => 1,
			],
			'rem' => [
				'min'  => 0.1,
				'max'  => 12,
				'step' => 0.1,
			],
			'em' => [
				'min'  => 0.1,
				'max'  => 12,
				'step' => 0.1,
			],
		];
	}

	/** @return array<string,array{min:float|int,max:float|int,step:float|int}> */
	public static function width_units(): array {
		return [
			'px' => [
				'min'  => 0,
				'max'  => 1000,
				'step' => 1,
			],
			'%' => [
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			],
			'rem' => [
				'min'  => 0,
				'max'  => 60,
				'step' => 0.1,
			],
			'em' => [
				'min'  => 0,
				'max'  => 60,
				'step' => 0.1,
			],
		];
	}
}
