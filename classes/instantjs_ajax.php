<?php
if ( !defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

class InstantJS_Ajax
{
    public function __construct()
    {
        add_action( 'wp_ajax_ijs_save_js', array( $this, 'ijs_ajax_save_js' ) );
        add_action( 'wp_ajax_ijs_get_js', array( $this, 'ijs_ajax_get_js' ) );

        add_action( 'wp_ajax_ijs_save_theme', array( $this, 'ijs_ajax_save_theme' ) );
        add_action( 'wp_ajax_ijs_get_theme', array( $this, 'ijs_ajax_get_theme' ) );

        add_action( 'wp_ajax_ijs_save_minify', array( $this, 'ijs_ajax_save_minify' ) );
        add_action( 'wp_ajax_ijs_get_minify', array( $this, 'ijs_ajax_get_minify' ) );
    }

    /**
     * Ajax action located in js/editor.js
     */
    public function ijs_ajax_get_js()
    {
        $savedJS = get_option( 'ijs_js' );
        $js = stripslashes($savedJS);

        if ( isset($js) ) {
            echo $js;
        } else {
            echo '';
        }

        wp_die();
    }

    public function ijs_ajax_save_js()
    {
        $js = $_POST['js'];
        $compiledjs = $_POST['compiledjs'];

        if ( isset($js) ) {
            update_option( 'ijs_js', $js, true );
            update_option( 'ijs_version', time(), true );
        } else {
            update_option( 'ijs_js', '', true );
            update_option( 'ijs_version', time(), true );
        }

        if ( isset($compiledjs) ) {
            update_option( 'ijs_compiledjs', $compiledjs, true );
            update_option( 'ijs_version', time(), true );
        } else {
            update_option( 'ijs_compiledjs', '', true );
            update_option( 'ijs_version', time(), true );
        }

        echo "Saved JS";

        wp_die();
    }

    public function ijs_ajax_save_theme()
    {
        $newTheme = $_POST['theme'];

        if ( isset($newTheme) ) {
            update_option( 'ijs_theme', $newTheme, true );
        } else {
            update_option( 'ijs_theme', 'vs', true );
        }

        echo "Saved theme";
        wp_die();
    }

    public function ijs_ajax_get_theme()
    {
        $theme = get_option( 'ijs_theme' );

        if ( isset($theme) ) {
            echo $theme;
        } else {
            echo 'vs';
        }

        wp_die();
    }

    public function ijs_ajax_save_minify()
    {
        $minifyOption = $_POST['minify'];

        if ( isset( $minifyOption ) ) {
            update_option( 'ijs_minify', $minifyOption, true );
        } else {
            update_option( 'ijs_minify', 'off', true );
        }

        echo "Saved minify option";
        wp_die();
    }

    public function ijs_ajax_get_minify()
    {
        $minify = get_option( 'ijs_minify' );

        if ( isset($minify) ) {
            echo $minify;
        } else {
            echo 'off';
        }

        wp_die();
    }
}
