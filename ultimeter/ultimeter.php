<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Ultimeter - the Ultimate Progress and Goals Meter
 * Plugin URI:        https://ultimeter.app
 * Description:       The most advanced progress and goals meter for WordPress
 * Version:           3.0.4
 * Author:            Bouncingsprout Studio
 * Author URI:        https://ultimeter.app
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ultimeter
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to:   8.7
 *
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( function_exists( 'upgm_fs' ) ) {
    upgm_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'upgm_fs' ) ) {
        /**
         * Create a helper function for easy SDK access.
         */
        function upgm_fs()
        {
            global  $upgm_fs ;
            
            if ( !isset( $upgm_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_1825_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_1825_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $upgm_fs = fs_dynamic_init( array(
                    'id'             => '1825',
                    'slug'           => 'ultimeter',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_08f596c6bccb4b5b1940526b0176a',
                    'is_premium'     => false,
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'slug'    => 'edit.php?post_type=ultimeter',
                    'support' => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $upgm_fs;
        }
        
        // Init Freemius.
        upgm_fs();
        // Signal that SDK was initiated.
        do_action( 'upgm_fs_loaded' );
    }
    
    // Current version.
    define( 'ULTIMETER_VERSION', '3.0.4' );
    define( 'ULTIMETER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    define( 'ULTIMETER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    // Define a constant to hold our support page.
    define( 'ULTIMETER_SUPPORT', 'https://ultimeter.app/docs/' );
    // Define a constant to hold our plugin page.
    define( 'ULTIMETER_PLUGIN', 'https://ultimeter.app' );
    /**
     * Initializes blank slate content if a list table is empty.
     *
     * @since 2.2
     */
    function ultimeter_blank_slate()
    {
        $blank_slate = new Ultimeter_Blank_Slate();
        $blank_slate->init();
    }
    
    add_action( 'current_screen', 'ultimeter_blank_slate' );
    /**
     * Prevent users from viewing directly from the posts table, as it doesn't make sense.
     *
     * @param Array $actions Array of actions.
     * @param Post  $post Post object.
     *
     * @return array
     */
    function ultimeter_post_row_actions( $actions, $post )
    {
        if ( 'ultimeter' === $post->post_type ) {
            unset( $actions['view'] );
        }
        return $actions;
    }
    
    add_filter(
        'post_row_actions',
        'ultimeter_post_row_actions',
        10,
        2
    );
    /**
     * Flush rewrite rules on activation.
     */
    function activate_ultimeter()
    {
        flush_rewrite_rules();
    }
    
    register_activation_hook( __FILE__, 'activate_ultimeter' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-ultimeter.php';
    /**
     * Start our autoloader for vendor libraries.
     */
    require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
    /**
     * Begins execution of the plugin.
     *
     * @since    1.0.0
     */
    function run_ultimeter()
    {
        $ultimeter = new Ultimeter();
    }
    
    run_ultimeter();
}
