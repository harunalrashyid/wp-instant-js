<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

class InstantJS
{
    /**
     *  InstantJS constructor
     */
    public function __construct()
    {
        // admin actions
        add_action( 'admin_menu', array( $this, 'ijs_create_admin_menu' ) );
        new InstantJS_Ajax();

        add_option( 'ijs_js', '', false, true );
        add_option( 'ijs_version', '', false, true );
        add_option( 'ijs_compiledjs', '', false, true );
        add_option( 'ijs_lang', 'css', false, true );
        add_option( 'ijs_theme', 'vs', false, true );
        add_option( 'ijs_minify', 'off', false, true );
        add_option( 'ijs_babel', 'off', false, true );

        // add saved option
        add_action( 'init', array( $this, 'ijs_get_js' ) );
    }

    public function ijs_create_admin_menu()
    {
        global $instantjs_page;
        $instantjs_page = add_menu_page(
            'Instant JS',
            'Instant JS',
            'manage_options',
            'instantjs',
            array( $this, 'ijs_show_admin_menu' ),
            'data:image/svg+xml;base64,' . base64_encode('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="code" class="svg-inline--fa fa-code fa-w-20" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="#89A1A6" d="M278.9 511.5l-61-17.7c-6.4-1.8-10-8.5-8.2-14.9L346.2 8.7c1.8-6.4 8.5-10 14.9-8.2l61 17.7c6.4 1.8 10 8.5 8.2 14.9L293.8 503.3c-1.9 6.4-8.5 10.1-14.9 8.2zm-114-112.2l43.5-46.4c4.6-4.9 4.3-12.7-.8-17.2L117 256l90.6-79.7c5.1-4.5 5.5-12.3.8-17.2l-43.5-46.4c-4.5-4.8-12.1-5.1-17-.5L3.8 247.2c-5.1 4.7-5.1 12.8 0 17.5l144.1 135.1c4.9 4.6 12.5 4.4 17-.5zm327.2.6l144.1-135.1c5.1-4.7 5.1-12.8 0-17.5L492.1 112.1c-4.8-4.5-12.4-4.3-17 .5L431.6 159c-4.6 4.9-4.3 12.7.8 17.2L523 256l-90.6 79.7c-5.1 4.5-5.5 12.3-.8 17.2l43.5 46.4c4.5 4.9 12.1 5.1 17 .6z"></path></svg>')
        );

        add_action( 'load-'.$instantjs_page, array( $this, 'ijs_plugin_page' ) );
    }

    /**
     * Get the template file for the admin page
     */
    public function ijs_show_admin_menu()
    {
        return $this->ijs_get_template( 'editor.php' );
    }

    /**
     * Enqueue all needed dependencies
     */
    public function ijs_admin_dependencies()
    {
        wp_enqueue_style( 'ijs-styles', plugins_url('assets/css/style.css', dirname(__FILE__)), array(), IJS_VERSION );
        wp_enqueue_script( 'ijs-vendor', plugins_url('assets/dist/vendors~main.bundle.js', dirname(__FILE__)), array(), IJS_VERSION, true );
        wp_enqueue_script( 'monaco-editor', plugins_url('assets/dist/main.bundle.js', dirname(__FILE__)), array(), IJS_VERSION, true );
        wp_localize_script( 'monaco-editor', 'wordpress', array(
            'plugins_url' => plugins_url('/', dirname(__FILE__)),
            'ajax_url' => admin_url('admin-ajax.php'),
            'is_customizer' => 'inactive'
        ) );
    }

    /**
     * Get values from the database using the options API
     */
    public function ijs_get_js()
    {
        $savedJS = get_option( 'ijs_compiledjs' );
        $js = stripslashes( $savedJS );

        if ( isset($js) ) {
            $jsFile = fopen( dirname(__DIR__) . '/public/custom.js', "w" );

            if ( isset($jsFile) ) {
                if ( fwrite( $jsFile, $js ) )
                    add_action( 'wp_enqueue_scripts',  array( $this, 'ijs_enqueue_js' ) );
            } else {
                echo '<script type="text/javascript">'. $js .'</script>';
            }
        }
    }

    /**
     * Enqueues the custom js file created by user
     */
    public function ijs_enqueue_js()
    {
        $version = get_option( 'ijs_version' );
        if ( isset($version) ) {
            wp_enqueue_script( 
                'ijs-custom-js', 
                plugins_url( 'public/custom.js', dirname(__FILE__) ),
                array(),
                $version,
                true
            );
        }
    }

    /**
     * Conditional scripts enqueuing
     */
    public function ijs_plugin_page()
    {
        global $instantjs_page;
        $screen = get_current_screen();

        // Check if current screen is instantjs page
        if ( $screen->id != $instantjs_page )
            return;

        add_action( 'admin_enqueue_scripts', array( $this, 'ijs_admin_dependencies' ) );
    }

    /**
     * @param $template_name
     */
    private function ijs_get_template( $template_name )
    {
        $template_file = $this->ijs_locate_template( $template_name );

        if( !file_exists( $template_file ) ) :
            _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
            return;
        endif;

        include $template_file;
    }

    /**
     * @param $template_name
     * @return mixed|void
     * Locate template file in templates folder
     */
    private function ijs_locate_template( $template_name )
    {
        $default_path = plugin_dir_path(__DIR__) . 'views/'; // path to the template folder

        // search template file in theme folder
        $template = locate_template( array(
            $template_name
        ) );

        // get plugin template file
        if ( !$template ) :
            $template = $default_path . $template_name;
        endif;

        return apply_filters( 'locate_template', $template, $template_name, $default_path );
    }
}