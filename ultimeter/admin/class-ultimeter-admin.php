<?php

/**
 * The file holding admin-specific functionality of the plugin.
 *
 * @since      2.2
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * The Ultimeter admin class.
 */
class Ultimeter_Admin
{
    /**
     * The icon for this plugin.
     *
     * @since    3.0
     * @access   private
     * @var      string $ultimeter The icon for this plugin.
     */
    private  $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="15.175" fill="#000" viewBox="0 0 12 11.382"><path d="M9.882 0.019c-0.034 0.006 -0.124 0.031 -0.201 0.049 -0.581 0.142 -1.123 0.696 -1.284 1.308 -0.068 0.272 -0.071 8.351 0 8.623 0.121 0.467 0.495 0.947 0.903 1.16a1.82 1.82 0 0 0 2.499 -0.801c0.207 -0.411 0.201 -0.243 0.201 -4.672 0 -4.454 0.01 -4.228 -0.214 -4.679 -0.154 -0.309 -0.312 -0.492 -0.581 -0.677a1.753 1.753 0 0 0 -0.968 -0.317c-0.161 -0.003 -0.321 -0.003 -0.356 0.006zM5.629 4.589c-0.69 0.145 -1.225 0.665 -1.407 1.364 -0.068 0.272 -0.071 3.745 0 4.02a1.836 1.836 0 0 0 0.495 0.866 1.808 1.808 0 0 0 2.796 -0.278c0.086 -0.13 0.189 -0.328 0.226 -0.442 0.071 -0.204 0.071 -0.226 0.081 -2.069 0.01 -2.054 0.006 -2.084 -0.182 -2.471 -0.362 -0.736 -1.215 -1.156 -2.007 -0.989zm-4.07 3.016c-0.751 0.121 -1.358 0.69 -1.516 1.42 -0.173 0.819 0.13 1.612 0.776 2.044a1.796 1.796 0 0 0 2.289 -0.226c0.381 -0.383 0.541 -0.786 0.541 -1.364 0 -0.371 -0.052 -0.597 -0.211 -0.912 -0.328 -0.662 -1.141 -1.076 -1.88 -0.962z"/></svg>' ;
    /**
     * Initialize the class and set its properties.
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
        require_once plugin_dir_path( __DIR__ ) . '/vendor/codestar-framework/codestar-framework.php';
        require_once plugin_dir_path( __DIR__ ) . '/admin/fields/connected_image_select.php';
        require_once plugin_dir_path( __DIR__ ) . '/admin/fields/image_select_style_packs.php';
        // Hooks.
        add_action( 'post_submitbox_misc_actions', array( $this, 'ultimeter_add_ID_to_publish_metabox' ) );
        add_action( 'post_submitbox_misc_actions', array( $this, 'ultimeter_add_shortcode_to_publish_metabox' ) );
        add_action( 'post_submitbox_misc_actions', array( $this, 'ultimeter_add_duplicate_button_metabox' ) );
        add_action( 'admin_head-post-new.php', array( $this, 'posttype_admin_css' ) );
        add_action( 'admin_head-post.php', array( $this, 'posttype_admin_css' ) );
        add_filter( 'post_updated_messages', array( $this, 'ultimeter_updated_messages' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ), 15 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'init', array( $this, 'ultimeter_post_type' ) );
        add_filter( 'admin_footer_text', array( $this, 'ultimeter_admin_rate_us' ) );
        add_filter( 'enter_title_here', array( $this, 'ultimeter_change_default_title' ) );
        add_action( 'admin_action_duplicate_ultimeter', array( $this, 'duplicate' ) );
        add_filter(
            'post_row_actions',
            array( $this, 'duplicate_link_row' ),
            10,
            2
        );
        add_action( 'wp_dashboard_setup', array( $this, 'ultimeter_dashboard_widget_init' ) );
        add_action( 'rest_endpoints', array( $this, 'disable_REST_defaults' ) );
        add_action( 'admin_init', array( 'PAnD', 'init' ) );
        add_action( 'plugins_loaded', array( $this, 'do_metabox' ) );
        add_action( 'plugins_loaded', array( $this, 'do_debug_metabox' ) );
        add_action( 'admin_notices', array( $this, 'do_notices' ) );
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    2.2
     */
    public function enqueue_styles()
    {
        if ( function_exists( 'get_current_screen' ) ) {
            
            if ( 'ultimeter' == get_current_screen()->post_type ) {
                wp_dequeue_style( 'give-admin-styles' );
                // CSS stylesheet for Color Picker
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_style(
                    'ultimeter',
                    ULTIMETER_PLUGIN_URL . 'admin/assets/css/ultimeter-admin.css',
                    array( 'wp-color-picker' ),
                    ULTIMETER_VERSION,
                    'all'
                );
            }
        
        }
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    2.2
     */
    public function enqueue_scripts()
    {
        if ( function_exists( 'get_current_screen' ) ) {
            
            if ( 'ultimeter' == get_current_screen()->post_type ) {
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'jquery-ui-slider' );
                wp_enqueue_script(
                    'ultimeter',
                    ULTIMETER_PLUGIN_URL . 'admin/assets/js/ultimeter-admin.js',
                    array( 'jquery', 'wp-color-picker', 'jquery-ui-slider' ),
                    ULTIMETER_VERSION,
                    false
                );
                wp_localize_script( 'ultimeter', 'ultimeter', array(
                    'ajaxurl'              => admin_url( 'admin-ajax.php' ),
                    'can_use_premium_code' => json_encode( upgm_fs()->can_use_premium_code() ),
                ) );
            }
        
        }
    }
    
    /**
     * Enqueue media.
     *
     * @since    2.2
     */
    public function enqueue_media()
    {
        if ( function_exists( 'get_current_screen' ) ) {
            if ( 'ultimeter' == get_current_screen()->post_type ) {
                if ( function_exists( 'wp_enqueue_media' ) ) {
                    wp_enqueue_media();
                }
            }
        }
    }
    
    public function do_notices()
    {
        global  $post ;
        
        if ( function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
            
            if ( 'ultimeter' == $screen->post_type && 'post' == $screen->base ) {
                if ( 'thermometer2020' === get_post_meta( $post->ID, '_ultimeter_meter_type', true ) || 'scalable' === get_post_meta( $post->ID, '_ultimeter_meter_type', true ) ) {
                    echo  '<div class="notice notice-warning is-dismissible">
      <p>Important: this meter type has been deprecated. Please choose another from the Meter Type menu below, to access the full range of meter options.</p>
      </div>' ;
                }
                if ( upgm_fs()->is_not_paying() ) {
                    if ( get_post_meta( $post->ID, '_ultimeter_meter_type', true ) && ('thermometer' !== get_post_meta( $post->ID, '_ultimeter_meter_type', true ) && 'progressbar' !== get_post_meta( $post->ID, '_ultimeter_meter_type', true )) ) {
                        echo  '<div class="notice notice-error is-dismissible">
      <p>Important: A valid licence is required to display this meter type. Please select either a thermometer or progress bar.</p>
      </div>' ;
                    }
                }
            }
        
        }
    
    }
    
    /**
     * Register the Ultimeter CPT.
     *
     * @since    2.2
     */
    public function ultimeter_post_type()
    {
        $labels = array(
            'name'                  => _x( 'Ultimeters', 'Post Type General Name', 'ultimeter' ),
            'singular_name'         => _x( 'Ultimeter', 'Post Type Singular Name', 'ultimeter' ),
            'menu_name'             => __( 'Ultimeter', 'ultimeter' ),
            'name_admin_bar'        => __( 'Ultimeter', 'ultimeter' ),
            'archives'              => __( 'Item Archives', 'ultimeter' ),
            'attributes'            => __( 'Item Attributes', 'ultimeter' ),
            'parent_item_colon'     => __( 'Parent Item:', 'ultimeter' ),
            'all_items'             => __( 'All Ultimeters', 'ultimeter' ),
            'add_new_item'          => __( 'Add New Ultimeter', 'ultimeter' ),
            'add_new'               => __( 'Add New Ultimeter', 'ultimeter' ),
            'new_item'              => __( 'New Ultimeter', 'ultimeter' ),
            'edit_item'             => __( 'Edit Ultimeter', 'ultimeter' ),
            'update_item'           => __( 'Update Ultimeter', 'ultimeter' ),
            'view_item'             => __( 'View Item', 'ultimeter' ),
            'view_items'            => __( 'View Items', 'ultimeter' ),
            'search_items'          => __( 'Search Item', 'ultimeter' ),
            'not_found'             => __( 'No Ultimeters found', 'ultimeter' ),
            'not_found_in_trash'    => __( 'No Ultimeters found in Trash', 'ultimeter' ),
            'featured_image'        => __( 'Featured Image', 'ultimeter' ),
            'set_featured_image'    => __( 'Set featured image', 'ultimeter' ),
            'remove_featured_image' => __( 'Remove featured image', 'ultimeter' ),
            'use_featured_image'    => __( 'Use as featured image', 'ultimeter' ),
            'insert_into_item'      => __( 'Insert into item', 'ultimeter' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'ultimeter' ),
            'items_list'            => __( 'Items list', 'ultimeter' ),
            'items_list_navigation' => __( 'Items list navigation', 'ultimeter' ),
            'filter_items_list'     => __( 'Filter items list', 'ultimeter' ),
        );
        $args = array(
            'label'                 => __( 'Ultimeter', 'ultimeter' ),
            'description'           => __( 'Post Type Description', 'ultimeter' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'taxonomies'            => array(),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode( $this->icon ),
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => false,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'show_in_rest'          => true,
            'rest_base'             => 'ultimeter',
            'rest_controller_class' => 'Ultimeter_REST_Controller',
            'capability_type'       => 'page',
            'register_meta_box_cb'  => array( $this, 'ultimeter_meta_box_init' ),
        );
        register_post_type( 'ultimeter', $args );
    }
    
    /**
     * Disable default REST API route to the Ultimeter to make way for custom endpoints.
     *
     * @param $endpoints
     *
     * @return mixed
     */
    public function disable_REST_defaults( $endpoints )
    {
        foreach ( $endpoints as $route => $endpoint ) {
            if ( 0 === stripos( $route, '/wp/v2/ultimeter' ) ) {
                unset( $endpoints[$route] );
            }
        }
        return $endpoints;
    }
    
    /**
     * Creates a duplicate Ultimeter.
     *
     * @since    2.2.4
     */
    public function duplicate()
    {
        global  $wpdb ;
        if ( !(isset( $_GET['post'] ) || isset( $_POST['post'] ) || isset( $_REQUEST['action'] ) && 'duplicate_post_as_draft' == $_REQUEST['action']) ) {
            wp_die( 'No post to duplicate has been supplied!' );
        }
        /*
         * Nonce verification
         */
        if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) ) {
            return;
        }
        /*
         * get the original post id
         */
        $post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
        /*
         * and all the original post data then
         */
        $post = get_post( $post_id );
        /*
         * set current user as new author
         */
        $current_user = wp_get_current_user();
        $new_post_author = $current_user->ID;
        /*
         * if post data exists, create the post duplicate
         */
        
        if ( isset( $post ) && $post != null ) {
            /*
             * new post data array
             */
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status'    => $post->ping_status,
                'post_author'    => $new_post_author,
                'post_content'   => $post->post_content,
                'post_excerpt'   => $post->post_excerpt,
                'post_name'      => $post->post_name,
                'post_parent'    => $post->post_parent,
                'post_password'  => $post->post_password,
                'post_status'    => 'draft',
                'post_title'     => $post->post_title . ' (copy)',
                'post_type'      => $post->post_type,
                'to_ping'        => $post->to_ping,
                'menu_order'     => $post->menu_order,
            );
            /*
             * insert the post by wp_insert_post() function
             */
            $new_post_id = wp_insert_post( $args );
            /*
             * get all current post terms ad set them to the new post draft
             */
            $taxonomies = get_object_taxonomies( $post->post_type );
            // returns array of taxonomy names for post type, ex array("category", "post_tag");
            foreach ( $taxonomies as $taxonomy ) {
                $post_terms = wp_get_object_terms( $post_id, $taxonomy, array(
                    'fields' => 'slugs',
                ) );
                wp_set_object_terms(
                    $new_post_id,
                    $post_terms,
                    $taxonomy,
                    false
                );
            }
            /*
             * duplicate all post meta just in two SQL queries
             */
            $post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id={$post_id}" );
            
            if ( count( $post_meta_infos ) != 0 ) {
                $sql_query = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) ";
                foreach ( $post_meta_infos as $meta_info ) {
                    $meta_key = $meta_info->meta_key;
                    if ( $meta_key == '_wp_old_slug' ) {
                        continue;
                    }
                    $meta_value = addslashes( $meta_info->meta_value );
                    $sql_query_sel[] = "SELECT {$new_post_id}, '{$meta_key}', '{$meta_value}'";
                }
                $sql_query .= implode( ' UNION ALL ', $sql_query_sel );
                $wpdb->query( $sql_query );
            }
            
            /*
             * finally, redirect to the edit post screen for the new draft
             */
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
            exit;
        } else {
            wp_die( 'Post creation failed, could not find original post: ' . $post_id );
        }
    
    }
    
    /**
     * Adds a duplicate link to row actions.
     *
     * @since    2.2.4
     */
    public function duplicate_link_row( $actions, $post )
    {
        // Check for your post type.
        if ( $post->post_type == 'ultimeter' && current_user_can( 'edit_posts' ) ) {
            $actions['duplicate'] = '<a href="' . wp_nonce_url( 'admin.php?action=duplicate_ultimeter&post=' . $post->ID, basename( __FILE__ ), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
        }
        return $actions;
    }
    
    /**
     * Initialise the Ultimeter CPT metaboxes.
     *
     * @since    2.2
     */
    public function ultimeter_meta_box_init()
    {
        if ( upgm_fs()->is_not_paying() || upgm_fs()->is_plan( 'pro', true ) ) {
            add_meta_box(
                'upgrade-ultimeter',
                __( 'Need More?', 'ultimeter' ),
                array( $this, 'ultimeter_upgrade_meta_box_content' ),
                'ultimeter',
                'side',
                'default'
            );
        }
    }
    
    /**
     * Render Ultimeter main meta box content.
     *
     * @param WP_Post $post The post object.
     *
     * @since    2.2
     */
    public function ultimeter_upgrade_meta_box_content()
    {
        require_once plugin_dir_path( __FILE__ ) . 'partials/ultimeter-upgrade-metabox.php';
    }
    
    /**
     * Create a metabox to collect debug settings.
     *
     * @return void
     */
    public function do_debug_metabox()
    {
        
        if ( class_exists( 'CSF' ) ) {
            $prefix = 'ultimeter_debug';
            CSF::createMetabox( $prefix, array(
                'title'     => esc_html__( 'Debug Mode', 'ultimeter' ),
                'post_type' => 'ultimeter',
                'data_type' => 'unserialize',
                'theme'     => 'light',
                'class'     => 'csf-ultimeter-debug',
                'context'   => 'side',
            ) );
            CSF::createSection( $prefix, array(
                'fields' => array( array(
                'id'    => 'ultimeter_debug_mode',
                'type'  => 'switcher',
                'title' => esc_html__( 'Switch on debug mode', 'ultimeter' ),
                'desc'  => esc_html__( 'Debug mode displays the configuration of your Ultimeter on the frontend, and it\'s events in the browser console.', 'ultimeter' ),
            ), array(
                'id'    => 'ultimeter_debug_quiet_mode',
                'type'  => 'switcher',
                'title' => esc_html__( 'Debug Quietly', 'ultimeter' ),
                'desc'  => esc_html__( 'Quiet mode prevents the debug information being displayed, useful if your Ultimeter is visible to the public.', 'ultimeter' ),
            ), array(
                'id'      => 'ultimeter_debug_information',
                'type'    => 'content',
                'title'   => esc_html__( 'Debug Information', 'ultimeter' ),
                'content' => $this->debug_information(),
            ) ),
            ) );
        }
    
    }
    
    /**
     * Adds a section for debug information. Can be used to check environment variables etc. Should not be used to debug individual meters.
     *
     * @return false|string
     */
    public function debug_information()
    {
        ob_start();
        ?>

		<?php 
        
        if ( wp_is_block_theme() ) {
            ?>

		<p><?php 
            esc_html_e( 'Block Theme Detected', 'ultimeter' );
            ?></p>

		<?php 
        } else {
            ?>

		<p><?php 
            esc_html_e( 'No debug information to show here', 'ultimeter' );
            ?></p>

		<?php 
        }
        
        ?>

		<?php 
        return ob_get_clean();
    }
    
    /**
     * Create a metabox to collect settings.
     *
     * @return void
     */
    public function do_metabox()
    {
        
        if ( class_exists( 'CSF' ) ) {
            $prefix = 'ultimeter';
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'integrations/woocommerce/includes/class-ultimeter-woocommerce.php';
            CSF::createMetabox( $prefix, array(
                'title'     => esc_html__( 'Configure your Ultimeter', 'ultimeter' ),
                'post_type' => 'ultimeter',
                'data_type' => 'unserialize',
                'theme'     => 'light',
                'class'     => 'csf-ultimeter',
            ) );
            $meters = Ultimeter_Meter_Type::get_instance();
            CSF::createSection( $prefix, array(
                'title'  => esc_html__( 'Meter Type', 'ultimeter' ),
                'class'  => 'meter-type',
                'icon'   => 'fas fa-thermometer-half',
                'fields' => array( array(
                'id'   => '_ultimeter_meter_type',
                'type' => 'connected_image_select',
                'sets' => $meters->get_CSF_meter_sets(),
            ) ),
            ) );
            CSF::createSection( $prefix, array(
                'title'  => esc_html__( 'Goal Options', 'ultimeter' ),
                'class'  => 'goal-options',
                'icon'   => 'fas fa-bullseye',
                'fields' => $this->get_goal_options_fields(),
            ) );
            CSF::createSection( $prefix, array(
                'title'  => esc_html__( 'Visual Options', 'ultimeter' ),
                'class'  => 'visual-options',
                'icon'   => 'fas fa-eye-dropper',
                'fields' => $this->get_styling_options_fields(),
            ) );
            CSF::createSection( $prefix, array(
                'title'  => esc_html__( 'Milestones', 'ultimeter' ),
                'class'  => 'milestones',
                'icon'   => 'fas fa-calendar-check',
                'fields' => $this->get_milestones_fields(),
            ) );
            CSF::createSection( $prefix, array(
                'title'  => esc_html__( 'Celebrations', 'ultimeter' ),
                'class'  => 'Celebrations',
                'icon'   => 'fas fa-gift',
                'fields' => $this->get_celebrations_fields(),
            ) );
            CSF::createSection( $prefix, array(
                'title'  => esc_html__( 'Custom Meter', 'ultimeter' ),
                'class'  => 'custom',
                'icon'   => 'fas fa-pencil-ruler',
                'fields' => $this->get_custom_meter_fields(),
            ) );
        }
    
    }
    
    public function get_styling_options_fields()
    {
        $r = array();
        $r[] = array(
            'id'    => '_ultimeter_meter_color',
            'type'  => 'color',
            'class' => 'color',
            'title' => esc_html__( 'Meter Primary Color', 'ultimeter' ),
            'desc'  => esc_html__( 'The main color for your meter.', 'ultimeter' ),
        );
        $r[] = array(
            'id'    => '_ultimeter_meter_outer_color',
            'type'  => 'color',
            'class' => 'background',
            'title' => esc_html__( 'Meter Secondary Color', 'ultimeter' ),
            'desc'  => esc_html__( 'The outer, or background color.', 'ultimeter' ),
        );
        $r[] = array(
            'id'    => '_ultimeter_meter_progress_color',
            'type'  => 'color',
            'title' => esc_html__( 'Meter Raised Amount Color', 'ultimeter' ),
            'desc'  => esc_html__( 'The color for the label or identifier of the amount raised.', 'ultimeter' ),
        );
        $r[] = array(
            'id'    => '_ultimeter_meter_goal_color',
            'type'  => 'color',
            'title' => esc_html__( 'Meter Total Amount Color', 'ultimeter' ),
            'desc'  => esc_html__( 'The color for the label or identifier of the total amount.', 'ultimeter' ),
        );
        $r[] = array(
            'id'      => '_ultimeter_meter_size',
            'type'    => 'button_set',
            'class'   => 'thermometer',
            'title'   => esc_html__( 'Select a Meter Size', 'ultimeter' ),
            'desc'    => esc_html__( 'Select a size for your Ultimeter.', 'ultimeter' ),
            'options' => array(
            'small'  => 'Small',
            'medium' => 'Medium',
            'large'  => 'Large',
        ),
            'default' => 'medium',
        );
        $r[] = array(
            'id'      => 'ultimeter_alignment',
            'type'    => 'button_set',
            'title'   => esc_html__( 'Horizontal Alignment', 'ultimeter' ),
            'desc'    => esc_html__( 'Align the Ultimeter within it\'s container.', 'ultimeter' ),
            'options' => array(
            'left'   => 'Left',
            'center' => 'Center',
            'right'  => 'Right',
        ),
            'default' => 'center',
        );
        $r[] = array(
            'id'      => '_ultimeter_progressbar_goal_toggle',
            'class'   => 'progressbar',
            'type'    => 'switcher',
            'default' => 0,
            'title'   => esc_html__( 'Display Goal', 'ultimeter' ),
            'desc'    => esc_html__( 'Choose to display the goal at the end of your Progress Bar.', 'ultimeter' ),
        );
        return apply_filters( 'ultimeter_admin_styling_options', $r );
    }
    
    public function get_goal_options_fields()
    {
        $r = array();
        $teaser_array = array();
        if ( class_exists( 'GFAPI' ) ) {
            $teaser_array[] = '<li>Gravity Forms</li>';
        }
        if ( class_exists( 'Give' ) ) {
            $teaser_array[] = '<li>GiveWP</li>';
        }
        if ( is_plugin_active( 'fluentform/fluentform.php' ) ) {
            $teaser_array[] = '<li>Fluent Forms</li>';
        }
        if ( class_exists( 'Charitable' ) ) {
            $teaser_array[] = '<li>Charitable</li>';
        }
        $teaser_html = '';
        if ( !empty($teaser_array) ) {
            $teaser_html = sprintf(
                '%1$s<ul>%2$s</ul>%3$s<a href="https://ultimeter.app/integrations">%4$s</a>%5$s',
                esc_html__( 'Hey there! It looks like you have plugins installed that are integrated into Ultimeter\'s Premium Edition, for example:', 'ultimeter' ),
                implode( '', $teaser_array ),
                esc_html__( 'If you are using any of these with Ultimeter, and are manually inputting data, take a look at our ', 'ultimeter' ),
                esc_html__( 'integrations', 'ultimeter' ),
                esc_html__( ' to see how much time you could save.', 'ultimeter' )
            );
        }
        $r[] = array(
            'type'    => 'notice',
            'class'   => 'plugin-teaser fluentform',
            'content' => $teaser_html,
        );
        $r[] = array(
            'id'      => '_ultimeter_goal_format',
            'type'    => 'radio',
            'title'   => esc_html__( 'Goal Format', 'ultimeter' ),
            'options' => array(
            'amount'     => esc_html__( 'Amount Raised', 'ultimeter' ),
            'percentage' => esc_html__( 'Percentage Raised', 'ultimeter' ),
        ),
        );
        $r[] = array(
            'id'      => '_ultimeter_language',
            'type'    => 'select',
            'desc'    => esc_html__( 'Select a language. The language you choose controls how decimals are displayed in currencies, as well as where any percentage sign is placed.', 'ultimeter' ),
            'chosen'  => true,
            'title'   => esc_html__( 'Language', 'ultimeter' ),
            'options' => ultimeter_get_languages(),
        );
        $r[] = array(
            'id'      => '_ultimeter_currency',
            'type'    => 'select',
            'desc'    => esc_html__( 'Select a Currency. The associated symbol or currency code will be used in the Ultimeter.', 'ultimeter' ),
            'chosen'  => true,
            'title'   => esc_html__( 'Currency', 'ultimeter' ),
            'options' => ultimeter_get_currencies(),
        );
        $r[] = array(
            'id'         => '_ultimeter_goal_amount',
            'type'       => 'number',
            'desc'       => esc_html__( 'Enter the goal amount. This is the total amount you hope to raise.', 'ultimeter' ),
            'title'      => esc_html__( 'Goal Amount', 'ultimeter' ),
            'dependency' => array( '_ultimeter_goal_format', '==', 'amount' ),
        );
        $r[] = array(
            'id'         => '_ultimeter_raised_amount',
            'type'       => 'number',
            'desc'       => esc_html__( 'Enter the raised amount. This is the amount of money you have raised so far.', 'ultimeter' ),
            'title'      => esc_html__( 'Raised Amount', 'ultimeter' ),
            'dependency' => array( '_ultimeter_goal_format', '==', 'amount' ),
        );
        $r[] = array(
            'id'         => '_ultimeter_raised_percentage',
            'type'       => 'number',
            'desc'       => esc_html__( 'Enter the percentage raised so far. The goal amount will always be 100%.', 'ultimeter' ),
            'unit'       => '%',
            'title'      => esc_html__( 'Raised Amount', 'ultimeter' ),
            'dependency' => array( '_ultimeter_goal_format', '==', 'percentage' ),
        );
        return apply_filters( 'ultimeter_admin_goal_options', $r );
    }
    
    public function get_milestones_fields()
    {
        $r = array();
        $r[] = array(
            'id'      => '_ultimeter_milestones_upsell',
            'type'    => 'content',
            'class'   => 'ultimeter-upsell-container',
            'content' => $this->milestones_upsell(),
        );
        return apply_filters( 'ultimeter_admin_milestones', $r );
    }
    
    public function get_celebrations_fields()
    {
        $r = array();
        $r[] = array(
            'id'      => '_ultimeter_celebrations_upsell',
            'type'    => 'content',
            'class'   => 'ultimeter-upsell-container',
            'content' => $this->celebrations_upsell(),
        );
        return apply_filters( 'ultimeter_admin_celebrations', $r );
    }
    
    public function get_custom_meter_fields()
    {
        $r = array();
        $r[] = array(
            'id'      => '_ultimeter_custom_upsell',
            'type'    => 'content',
            'class'   => 'ultimeter-upsell-container',
            'content' => $this->custom_upsell(),
        );
        return apply_filters( 'ultimeter_admin_custom_meter', $r );
    }
    
    /**
     * Renders an upsell container.
     *
     * @return false|string
     */
    public function custom_upsell()
    {
        ob_start();
        ?>

		<div class="ultimeter-upsell">
			<div class="ultimeter-upsell-left">
				<h2>Create your own meter</h2>
				<p>Use your company or organisation's own logo or any image you choose. Upload an empty version, and a filled version, and Ultimeter will handle the rest. Use your meter to track any of the default data types. Add a dynamic counter to the meter.</p>
				<a class="ultimeter-upsell-button button" href="<?php 
        echo  upgm_fs()->get_upgrade_url() ;
        ?>">Upgrade Now</a>
			</div>
			<div class="ultimeter-upsell-right">
				<img src="<?php 
        echo  plugin_dir_url( __DIR__ ) . 'admin/assets/images/custom-upsell.png' ;
        ?>">
			</div>
		</div>

		<?php 
        return ob_get_clean();
    }
    
    /**
     * Renders an upsell container.
     *
     * @return false|string
     */
    public function milestones_upsell()
    {
        ob_start();
        ?>

		<div class="ultimeter-upsell">
			<div class="ultimeter-upsell-left">
				<h2>Milestones</h2>
				<p>One of Ultimeter's most popular features, milestones, allow you to display key events inside your meter. Add as many as you need, using most of the pre-built meter types. Imagine you are building a school. Why not show when the foundations are dug, the walls go up, and the roof is put on? Acknowledging milestones motivate supporters to keep up their efforts.</p>
				<a class="ultimeter-upsell-button button" href="<?php 
        echo  upgm_fs()->get_upgrade_url() ;
        ?>">Upgrade Now</a>
			</div>
			<div class="ultimeter-upsell-right">
				<img src="<?php 
        echo  plugin_dir_url( __DIR__ ) . 'admin/assets/images/milestones-upsell.png' ;
        ?>">
			</div>
		</div>

		<?php 
        return ob_get_clean();
    }
    
    /**
     * Renders an upsell container.
     *
     * @return false|string
     */
    public function celebrations_upsell()
    {
        ob_start();
        ?>

		<div class="ultimeter-upsell">
			<div class="ultimeter-upsell-left">
				<h2>Celebrate your progress in style</h2>
				<p>Why not include an animated confetti effect when your visitors see your progress hit the top of your meter? Control the size and duration of the effect to suit your site.</p>
				<a class="ultimeter-upsell-button button" href="<?php 
        echo  upgm_fs()->get_upgrade_url() ;
        ?>">Upgrade Now</a>
			</div>
			<div class="ultimeter-upsell-right">
				<img src="<?php 
        echo  plugin_dir_url( __DIR__ ) . 'admin/assets/images/celebrations-upsell.png' ;
        ?>">
			</div>
		</div>

		<?php 
        return ob_get_clean();
    }
    
    /**
     * Add rating links to the admin dashboard
     *
     * @param string $footer_text The existing footer text
     *
     * @return    string
     * @since    2.2
     * @global    string $typenow , $pagenow
     */
    function ultimeter_admin_rate_us( $footer_text )
    {
        global  $typenow ;
        
        if ( 'ultimeter' === $typenow ) {
            $rate_text = sprintf(
                /* translators: %s: Link to 5 star rating */
                __( 'If you like <strong>Ultimeter</strong> please leave us a %s rating. It takes a minute and helps a lot. Thanks in advance!', 'ultimeter' ),
                '<a href="https://wordpress.org/support/view/plugin-reviews/ultimeter?filter=5#postform" target="_blank" class="ultimeter-rating-link" style="text-decoration:none;" data-rated="' . esc_attr__( 'Thanks :)', 'ultimeter' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
            );
            return $rate_text;
        } else {
            return $footer_text;
        }
    
    }
    
    /**
     * Change default Add title input
     *
     * @param string $title Default title placeholder text
     *
     * @return string $title New placeholder text
     * @since 2.2
     */
    function ultimeter_change_default_title( $title )
    {
        $screen = get_current_screen();
        if ( 'ultimeter' == $screen->post_type ) {
            $title = __( 'Enter Ultimeter name here', 'give' );
        }
        return $title;
    }
    
    /**
     * Add Shortcode Copy Field to Publish Metabox
     *
     * @since: 2.2
     */
    public function ultimeter_add_shortcode_to_publish_metabox()
    {
        if ( 'ultimeter' !== get_post_type() ) {
            return false;
        }
        global  $post ;
        // Only enqueue scripts for CPT on post type screen
        
        if ( 'ultimeter' === $post->post_type ) {
            // Shortcode column with select all input
            $shortcode = sprintf( "[ultimeter id='%s']", absint( $post->ID ) );
            printf(
                '<div class="misc-pub-section"><button type="button" class="button ultimeter-shortcode-button" aria-label="%1$s" title="Copy shortcode to insert into a post or page" data-clipboard-text="%2$s"><span class="dashicons dashicons-admin-page"></span> %3$s</button></div>',
                esc_attr( $shortcode ),
                esc_attr( $shortcode ),
                esc_html__( 'Copy Shortcode', 'ultimeter' )
            );
        }
    
    }
    
    /**
     * Add ID Field to Publish Metabox
     *
     * @since: 2.6.0
     */
    public function ultimeter_add_ID_to_publish_metabox( $post )
    {
        if ( 'ultimeter' !== get_post_type() ) {
            return false;
        }
        // Only enqueue scripts for CPT on post type screen
        
        if ( 'ultimeter' === $post->post_type ) {
            // Shortcode column with select all input
            $post_id = absint( $post->ID );
            printf(
                // translators: %s: ID of this Ultimeter
                __( '<div class="misc-pub-section ultimeter-ID"><span> Ultimeter ID: <strong>%s</strong></span></div>', 'ultimeter' ),
                esc_attr( $post_id )
            );
        }
    
    }
    
    /**
     * Initialise dashboard widget
     *
     * @since: 2.2.4
     */
    public function ultimeter_dashboard_widget_init()
    {
        global  $wp_meta_boxes ;
        wp_add_dashboard_widget( 'ultimeter_dashboard_widget', 'Ultimeter Status and News', array( $this, 'ultimeter_dashboard_widget_content' ) );
    }
    
    /**
     * Populate dashboard widget
     *
     * @since: 2.2.4
     */
    public function ultimeter_dashboard_widget_content( $post, $callback_args )
    {
        global  $wpdb ;
        $count = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = 'ultimeter' and post_status = 'publish'" );
        $url = admin_url( 'edit.php?post_type=ultimeter' );
        $logo = plugin_dir_url( __FILE__ ) . 'assets/images/logo.svg';
        $support_url = 'https://wordpress.org/support/plugin/ultimeter/';
        echo  "<img src='{$logo}' width='20' style='margin-right: 5px;vertical-align: middle;'><a href='{$url}' style='text-decoration: none;'>You have {$count}  Ultimeters ready to use</a>" ;
        echo  "<p>Need help? Contact us <a href='{$support_url}' target='_blank' style='text-decoration: none;'>here</a>. For support and FAQs visit the <a href='" . ULTIMETER_SUPPORT . "' target='_blank' style='text-decoration: none;'> Ultimeter Knowledgebase</a>.</p>" ;
        echo  '<h3><strong>Latest News</strong></h3>' ;
        // Get RSS Feed(s)
        include_once ABSPATH . WPINC . '/feed.php';
        // Get a SimplePie feed object from the specified feed source.
        $rss = fetch_feed( 'https://ultimeter.app/tag/news/feed/ ' );
        $maxitems = 0;
        
        if ( !is_wp_error( $rss ) ) {
            // Checks that the object is created correctly
            // Figure out how many total items there are, but limit it to 5.
            $maxitems = $rss->get_item_quantity( 5 );
            // Build an array of all the items, starting with element 0 (first element).
            $rss_items = $rss->get_items( 0, $maxitems );
        }
        
        
        if ( $maxitems == 0 ) {
            echo  '<h3>Nope. No news. But check back soon!</h3>' ;
        } else {
            foreach ( $rss_items as $item ) {
                $url = esc_url( $item->get_permalink() );
                $title = esc_html( $item->get_title() );
                echo  "<p><a href='{$url}' style='text-decoration: none;' target='_blank'>{$title}</a></p>" ;
            }
        }
    
    }
    
    /**
     * Add Duplicate Button to Publish Metabox
     *
     * @since: 2.2.4
     */
    public function ultimeter_add_duplicate_button_metabox()
    {
        if ( 'ultimeter' !== get_post_type() ) {
            return false;
        }
        global  $post ;
        // Only enqueue scripts for CPT on post type screen
        if ( 'ultimeter' === $post->post_type ) {
            if ( current_user_can( 'edit_posts' ) ) {
                printf( '<div class="misc-pub-section"><a href="' . wp_nonce_url( 'admin.php?action=duplicate_ultimeter&post=' . $post->ID, basename( __FILE__ ), 'duplicate_nonce' ) . '" title="Create a new Ultimeter based on this one" rel="permalink"><button type="button" class="button ultimeter-duplicate-button"><span class="dashicons dashicons-welcome-add-page"></span> %1$s</button></a></div>', esc_html__( 'Duplicate', 'ultimeter' ) );
            }
        }
    }
    
    /**
     * Update messages
     *
     * @since: 2.2
     */
    public function ultimeter_updated_messages( $messages )
    {
        $post = get_post();
        $post_type = get_post_type( $post );
        $post_type_object = get_post_type_object( $post_type );
        $messages['ultimeter'] = array(
            0  => '',
            1  => __( 'Ultimeter updated.', 'ultimeter' ),
            2  => __( 'Custom field updated.', 'ultimeter' ),
            3  => __( 'Custom field deleted.', 'ultimeter' ),
            4  => __( 'Ultimeter updated.', 'ultimeter' ),
            5  => ( isset( $_GET['revision'] ) ? sprintf( __( 'Ultimeter restored to revision from %s', 'ultimeter' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false ),
            6  => __( 'Ultimeter published.', 'ultimeter' ),
            7  => __( 'Ultimeter saved.', 'ultimeter' ),
            8  => __( 'Ultimeter submitted.', 'ultimeter' ),
            9  => sprintf(
            __( 'Ultimeter scheduled for: <strong>%1$s</strong>.', 'ultimeter' ),
            // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i', 'ultimeter' ), strtotime( $post->post_date ) )
        ),
            10 => __( 'Ultimeter draft updated.', 'ultimeter' ),
        );
        
        if ( $post_type_object->publicly_queryable && 'ultimeter' === $post_type ) {
            $helper = sprintf( __( ' To use your new Ultimeter, insert the following shortcode into a page: [ultimeter id="%s"], use the \'Copy Shortcode\' button below, add an Ultimeter Widget, or use the all new Ultimeter Block. The choice is yours.', 'ultimeter' ), $post->ID );
            $messages[$post_type][1] .= $helper;
            $messages[$post_type][6] .= $helper;
            $messages[$post_type][9] .= $helper;
            $messages[$post_type][8];
            $messages[$post_type][10];
        }
        
        return $messages;
    }
    
    /**
     * Remove preview buttons in Publish Metabox
     *
     * @since: 2.2
     */
    public function posttype_admin_css()
    {
        global  $post_type ;
        $post_types = array( 'ultimeter' );
        if ( in_array( $post_type, $post_types ) ) {
            echo  '<style>#post-preview, #edit-slug-box, #view-post-btn{display: none;}</style>' ;
        }
    }

}
$ultimeter_admin = new Ultimeter_Admin();