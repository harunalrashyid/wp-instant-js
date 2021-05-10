<?php

/**
 * Plugin Name: Instant JS
 * Description: Write your scirpts beautifully with the power of Visual Studio Code
 * Version:     1.0.0
 * Author:      Harun
 * Author URI:  https://github.com/harunalrashyid/instantjs-wp
 * License:     GPL-3.0
 * License URI: https://raw.githubusercontent.com/harunalrashyid/instantjs-wp/master/LICENSE
 */

define('IJS_VERSION', '1.0.0');

if ( !defined( 'ABSPATH' ) ) {
    die( 'Access Denied' );
}

require_once( __DIR__ . '/classes/instantjs.php' );
require_once( __DIR__ . '/classes/instantjs_ajax.php' );

if ( class_exists( 'InstantJS' ) ) {
    $instantJS = new InstantJS();
}