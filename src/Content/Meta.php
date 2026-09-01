<?php
declare(strict_types=1);

namespace CB\Dictionary\Content;

defined( 'ABSPATH' ) || exit;

final class Meta {
	public const PRONUNCIATION = 'cb_dictionary_pronunciation';
	public const ABBREVIATION  = 'cb_dictionary_abbreviation';
	public const SYNONYMS      = 'cb_dictionary_synonyms';
	public const SOURCE        = 'cb_dictionary_source';
	public const FEATURED      = 'cb_dictionary_featured';

	public static function register(): void {
		foreach ( [ self::PRONUNCIATION, self::ABBREVIATION, self::SYNONYMS, self::SOURCE ] as $key ) {
			register_post_meta( PostType::TYPE, $key, self::string_args() );
		}

		register_post_meta(
			PostType::TYPE,
			self::FEATURED,
			[
				'single'            => true,
				'type'              => 'boolean',
				'default'           => false,
				'show_in_rest'      => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => [ __CLASS__, 'can_edit_meta' ],
			]
		);
	}

	/** @return array<string,mixed> */
	private static function string_args(): array {
		return [
			'single'            => true,
			'type'              => 'string',
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => [ __CLASS__, 'can_edit_meta' ],
		];
	}

	public static function can_edit_meta( mixed $allowed, string $meta_key, int $post_id ): bool {
		unset( $allowed, $meta_key );
		return $post_id > 0 && current_user_can( 'edit_post', $post_id );
	}
}
