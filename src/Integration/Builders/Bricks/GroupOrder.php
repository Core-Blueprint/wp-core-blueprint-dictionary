<?php
declare(strict_types=1);
namespace CB\Dictionary\Integration\Builders\Bricks;
defined( 'ABSPATH' ) || exit;
final class GroupOrder {private const P='Core Blueprint ';public static function init():void{add_filter('bricks/dynamic_tags_list',[self::class,'normalize'],9999);}public static function normalize(array $tags):array{$c=[];$o=[];$i=null;foreach($tags as $t){if(is_array($t)&&isset($t['group'])&&is_string($t['group'])&&str_starts_with($t['group'],self::P)){$i??=count($o);$c[]=$t;}else{$o[]=$t;}}if(null===$i||[]===$c){return array_values($tags);}array_splice($o,$i,0,$c);return array_values($o);}}
