<?php
declare(strict_types=1);
namespace CB\Dictionary\Frontend;
use CB\Dictionary\Content\Meta;
use CB\Dictionary\Content\PostType;
defined( 'ABSPATH' ) || exit;
final class Conditions { public static function is_entry(int $post_id):bool{return PostType::TYPE===get_post_type($post_id);} public static function is_featured(int $post_id):bool{return self::is_entry($post_id)&&(bool)get_post_meta($post_id,Meta::FEATURED,true);} public static function has_term(int $post_id,string $taxonomy,string $slug):bool{return self::is_entry($post_id)&&''!==sanitize_title($slug)&&has_term(sanitize_title($slug),$taxonomy,$post_id);} }
