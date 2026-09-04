<?php
declare(strict_types=1);
namespace CB\Dictionary\Frontend;
use CB\Dictionary\Content\Meta;
use CB\Dictionary\Content\PostType;
use CB\Dictionary\Content\Taxonomies;
defined( 'ABSPATH' ) || exit;
final class Data {
	public static function entry(int $post_id):array{$post=get_post($post_id);if(!$post instanceof \WP_Post||PostType::TYPE!==$post->post_type){return [];}return ['record_type'=>'dictionary_entry','entry_id'=>(int)$post->ID,'title'=>(string)get_the_title($post),'url'=>(string)get_permalink($post),'pronunciation'=>(string)get_post_meta($post->ID,Meta::PRONUNCIATION,true),'abbreviation'=>(string)get_post_meta($post->ID,Meta::ABBREVIATION,true),'synonyms'=>(string)get_post_meta($post->ID,Meta::SYNONYMS,true),'source'=>(string)get_post_meta($post->ID,Meta::SOURCE,true),'featured'=>(bool)get_post_meta($post->ID,Meta::FEATURED,true),'categories'=>self::term_names($post->ID,Taxonomies::CATEGORY),'tags'=>self::term_names($post->ID,Taxonomies::TAG),'letters'=>self::term_names($post->ID,Taxonomies::LETTER),'category_slugs'=>self::term_slugs($post->ID,Taxonomies::CATEGORY),'tag_slugs'=>self::term_slugs($post->ID,Taxonomies::TAG),'letter_slugs'=>self::term_slugs($post->ID,Taxonomies::LETTER)];}
	private static function term_names(int $post_id,string $taxonomy):string{$terms=wp_get_object_terms($post_id,$taxonomy);if(is_wp_error($terms)){return '';}return implode(', ',array_map(static fn(\WP_Term $term):string=>(string)$term->name,array_filter((array)$terms,static fn($term):bool=>$term instanceof \WP_Term)));}
	private static function term_slugs(int $post_id,string $taxonomy):string{$terms=wp_get_object_terms($post_id,$taxonomy);if(is_wp_error($terms)){return '';}return implode(',',array_map(static fn(\WP_Term $term):string=>(string)$term->slug,array_filter((array)$terms,static fn($term):bool=>$term instanceof \WP_Term)));}
}
