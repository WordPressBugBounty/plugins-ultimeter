<?php

/**
 * File that holds our core class.
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * The core plugin class.
 *
 * All plugin-wide and public-facing functionality lives here.
 */
class Ultimeter
{
    /**
     * Our constructor.
     */
    public function __construct()
    {
        $this->init();
    }
    
    /**
     * Initialise the class's dependencies and hooks.
     *
     * @return void
     */
    public function init()
    {
        // Dependencies.
        require_once ULTIMETER_PLUGIN_PATH . 'includes/class-ultimeter-blank-slate.php';
        require_once ULTIMETER_PLUGIN_PATH . 'includes/class-ultimeter-ultimeter.php';
        require_once ULTIMETER_PLUGIN_PATH . 'admin/class-ultimeter-admin.php';
        require_once ULTIMETER_PLUGIN_PATH . 'gutenberg/class-ultimeter-gutenberg-block.php';
        require_once ULTIMETER_PLUGIN_PATH . 'includes/class-ultimeter-widget.php';
        require_once ULTIMETER_PLUGIN_PATH . 'includes/class-ultimeter-rest-controller.php';
        require_once ULTIMETER_PLUGIN_PATH . 'includes/class-ultimeter-meter-type.php';
        require_once ULTIMETER_PLUGIN_PATH . 'includes/class-ultimeter-style-pack.php';
        require_once ULTIMETER_PLUGIN_PATH . 'includes/ultimeter-functions.php';
        // Hooks.
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'init', array( $this, 'register_shortcodes' ) );
    }
    
    /**
     * Load plugin textdomain for i18n features.
     *
     * @return void
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain( 'ultimeter', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            'ultimeter',
            plugin_dir_url( __DIR__ ) . 'assets/css/ultimeter-public.css',
            array(),
            ULTIMETER_VERSION,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    2.2
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'ultimeter',
            plugin_dir_url( __DIR__ ) . 'assets/js/ultimeter-public-free.js',
            array( 'jquery' ),
            ULTIMETER_VERSION,
            true
        );
    }
    
    /**
     * Register our shortcode
     *
     * @since    2.2
     */
    public function register_shortcodes()
    {
        if ( !is_admin() ) {
            add_shortcode( 'ultimeter', array( $this, 'create_shortcode' ) );
        }
    }
    
    /**
     * Create our main shortcode
     *
     * @param Array $atts Array of shortcode attributes.
     *
     * @return false|string|void|null
     */
    public function create_shortcode( $atts )
    {
        $hasposts = get_posts( 'post_type=ultimeter' );
        if ( empty($hasposts) ) {
            return;
        }
        $a = shortcode_atts( array(
            'id' => '',
        ), $atts );
        if ( empty(array_filter( $a )) ) {
            return '<p><strong>Please ensure your Ultimeter shortcode contains an ID</strong></p>';
        }
        $ultimeter = new Ultimeter_Ultimeter( $a['id'] );
        $css = $ultimeter->get_inline_css();
        wp_register_style(
            'ultimeter-inline',
            false,
            null,
            ULTIMETER_VERSION
        );
        wp_enqueue_style( 'ultimeter-inline' );
        wp_add_inline_style( 'ultimeter-inline', $css );
        return $ultimeter->render();
    }

}