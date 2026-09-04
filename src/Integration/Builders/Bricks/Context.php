<?php
declare(strict_types=1);
namespace CB\Dictionary\Integration\Builders\Bricks;
use CB\Dictionary\Content\PostType;
use CB\Dictionary\Frontend\Data;
defined( 'ABSPATH' ) || exit;
final class Context { public static function entry_id():int{$loop=null;if(class_exists('\\Bricks\\Query')&&method_exists('\\Bricks\\Query','get_loop_object')){$loop=\Bricks\Query::get_loop_object();}if($loop instanceof \WP_Post&&PostType::TYPE===$loop->post_type){return (int)$loop->ID;}if(is_object($loop)&&isset($loop->ID)&&PostType::TYPE===get_post_type((int)$loop->ID)){return (int)$loop->ID;}$id=absint(get_the_ID());return PostType::TYPE===get_post_type($id)?$id:0;} public static function record():array{$id=self::entry_id();return $id>0?Data::entry($id):[];} }
