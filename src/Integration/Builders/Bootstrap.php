<?php
declare(strict_types=1);
namespace CB\Dictionary\Integration\Builders;
use CB\Dictionary\Integration\Builders\Bricks\Bootstrap as BricksBootstrap;
defined( 'ABSPATH' ) || exit;
final class Bootstrap { private static bool $registered=false; public static function init():void{if(self::$registered){return;}self::$registered=true;add_action('init',[self::class,'boot'],30);} public static function boot():void{if(!defined('BRICKS_VERSION')){return;}BricksBootstrap::init();} }
