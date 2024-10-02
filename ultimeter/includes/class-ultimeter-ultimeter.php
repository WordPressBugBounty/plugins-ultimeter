<?php

/**
 * The file that holds the class that describes an Ultimeter object.
 */
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
/**
 * The class that describes an Ultimeter object.
 */
class Ultimeter_Ultimeter
{
    /**
     * ID of the Ultimeter.
     *
     * @var int
     */
    public  $id ;
    /**
     * Name of Ultimeter.
     *
     * @var string
     */
    protected  $name ;
    /**
     * Meter type of this Ultimeter.
     *
     * @var string
     */
    protected  $type ;
    /**
     * Goal format of this Ultimeter.
     *
     * @var string
     */
    protected  $format ;
    /**
     * Output type for this Ultimeter.
     *
     * @var string
     */
    protected  $output_type ;
    /**
     * Current amount for this Ultimeter.
     *
     * @var string
     */
    protected  $current ;
    /**
     * Total amount for this Ultimeter.
     *
     * @var string
     */
    protected  $total ;
    /**
     * Language of this Ultimeter.
     *
     * @var mixed|string
     */
    protected  $language ;
    /**
     * Currency of this Ultimeter.
     *
     * @var mixed|string
     */
    protected  $currency ;
    /**
     * Progress of this Ultimeter.
     *
     * @var float|int|string
     */
    protected  $progress ;
    /**
     * Physics for this Ultimeter.
     *
     * @var mixed|string
     */
    protected  $physics ;
    /**
     * Styles for this Ultimeter.
     *
     * @var array
     */
    protected  $styles ;
    /**
     * Does this Ultimeter have a goal?
     *
     * @var bool
     */
    protected  $is_goalless ;
    /**
     * Constructor.
     *
     * @param int|null $id  Optional. If the ID of an existing Ultimeter is provided,
     *                      the object will be pre-populated with info about that Ultimeter.
     */
    public function __construct( $id = null )
    {
        $this->init();
        
        if ( !empty($id) ) {
            $this->id = (int) $id;
            $this->populate();
        }
    
    }
    
    /**
     * Set up data about the current Ultimeter.
     *
     * @return void
     */
    public function populate()
    {
        $ultimeter = get_post( $this->id );
        // Ensure we really do have an Ultimeter.
        
        if ( 'ultimeter' !== $ultimeter->post_type ) {
            $this->id = 0;
            return;
        }
        
        // Ultimeter found so set up the object variables.
        $this->name = stripslashes( $ultimeter->post_title );
        $this->type = $this->get_type();
        $this->format = get_post_meta( $this->id, '_ultimeter_goal_format', true );
        $this->output_type = $this->get_output_type();
        $this->physics = $this->get_physics();
        $this->language = get_post_meta( $this->id, '_ultimeter_language', true ) ?? 'en';
        $this->currency = get_post_meta( $this->id, '_ultimeter_currency', true ) ?? 'USD';
        $this->is_goalless = $this->is_goalless();
    }
    
    /**
     * Empty method so inherited classes can add actions and filters.
     *
     * @return void
     */
    protected function init()
    {
    }
    
    /**
     * Gets the meter type for this Ultimeter.
     *
     * @return mixed
     */
    public function get_type()
    {
        $type = get_post_meta( $this->id, '_ultimeter_meter_type', true );
        if ( !$type ) {
            $type = 'thermometer';
        }
        $this->type = $type;
        return $type;
    }
    
    /**
     * Gets the format for this Ultimeter.
     *
     * @return mixed
     */
    public function get_format()
    {
        return $this->format;
    }
    
    /**
     * Gets the language for this Ultimeter.
     *
     * @return mixed|string
     */
    public function get_language()
    {
        return $this->language;
    }
    
    /**
     * Gets the currency for this Ultimeter.
     *
     * @return mixed|string
     */
    public function get_currency()
    {
        return $this->currency;
    }
    
    /**
     * Gets the meter type data for this Ultimeter.
     *
     * @return false|mixed
     */
    public function get_meter_type_data()
    {
        $meter_types = Ultimeter_Meter_Type::get_instance();
        return $meter_types->get_meter_type( $this->type );
    }
    
    /**
     * Checks to see whether this Ultimeter belongs to a meter type that has been declared as goalless. For example, infinite meters.
     *
     * @return bool
     */
    private function is_goalless()
    {
        $data = $this->get_meter_type_data();
        if ( isset( $data['goalless'] ) && $data['goalless'] ) {
            return true;
        }
        return false;
    }
    
    /**
     * Gets the physics for this meter. The physics defines how the meter behaves when progress is added.
     * Generally, thermometers rise, progress bars move from left to right, and radial meters rotate.
     *
     * @return mixed|string
     */
    public function get_physics()
    {
        $data = $this->get_meter_type_data();
        $physics = $data['physics'] ?? 'height';
        $this->physics = $physics;
        return $physics;
    }
    
    /**
     * Get the current (raised) value for this Ultimeter.
     *
     * @return int
     */
    public function get_current()
    {
        // Set a default.
        $default = apply_filters( 'ultimeter_default_current', 0 );
        switch ( $this->format ) {
            case 'amount':
            default:
                $current = get_post_meta( $this->id, '_ultimeter_raised_amount', true );
                break;
            case 'percentage':
                $current = get_post_meta( $this->id, '_ultimeter_raised_percentage', true );
                break;
        }
        // Lastly, if we still can't find a current (raised amount), fallback to the raised amount entry, and finally the default.
        if ( !$current ) {
            $current = $default;
        }
        // Clean any unwanted commas.
        $current = (double) str_replace( ',', '', $current );
        $this->current = $current;
        return $current;
    }
    
    /**
     * Get the total for this Ultimeter.
     *
     * @return mixed|void|null
     */
    public function get_total()
    {
        // Set a default.
        $default = apply_filters( 'ultimeter_default_total', 100 );
        switch ( $this->format ) {
            case 'amount':
            default:
                $total = get_post_meta( $this->id, '_ultimeter_goal_amount', true );
                break;
            case 'percentage':
                $total = 100;
                break;
        }
        // Lastly, if we still can't find a total, fallback to the goal amount entry, and finally the default.
        if ( !$total ) {
            $total = get_post_meta( $this->id, '_ultimeter_goal_amount', true ) ?? $default;
        }
        // Clean any unwanted commas.
        $total = (double) str_replace( ',', '', $total );
        $this->total = $total;
        return $total;
    }
    
    /**
     * Gets the progress for this Ultimeter. This is used for rendering the raised amount.
     *
     * @return float|int|string
     */
    public function get_progress()
    {
        
        if ( 'infinite' === $this->type ) {
            // Progress will always be current, as there is no total.
            $progress = $this->current;
        } elseif ( 0 == $this->total ) {
            // If not an infinite meter, there should be a total.
            $this->progress = 0;
            return 0;
        } else {
            $output_type = $this->output_type;
            switch ( $output_type ) {
                case 'ultimeter_currency':
                case 'ultimeter_custom':
                default:
                    $progress = $this->current * 100 / $this->total;
                    break;
                case 'ultimeter_percentage':
                    $progress = $this->current;
                    break;
            }
        }
        
        $this->progress = $progress;
        return $progress;
    }
    
    /**
     * The output type controls how the values are rendered on the front end. It should always be one of 3 types:
     * ultimeter_currency, ultimeter_percentage, or ultimeter_custom.
     *
     * @return string
     */
    public function get_output_type()
    {
        switch ( $this->format ) {
            case 'amount':
            default:
                $output_type = 'ultimeter_currency';
                break;
            case 'percentage':
                $output_type = 'ultimeter_percentage';
                break;
        }
        return $output_type;
    }
    
    /**
     * Get the style pack for this Ultimeter.
     *
     * @param string $pack The name of the style pack.
     *
     * @return false|mixed
     */
    public function get_the_style_pack( $pack = null )
    {
        if ( !$pack && $this->style_pack ) {
            $pack = $this->style_pack;
        }
        if ( !$pack ) {
            return false;
        }
        $style_packs = Ultimeter_Style_Pack::get_instance();
        return $style_packs->get_style_pack( $pack );
    }
    
    /**
     * Gets the alignment for this Ultimeter, preserving legacy forced centering.
     *
     * @return mixed|string
     */
    public function get_alignment()
    {
        $force_centering = get_post_meta( $this->id, '_ultimeter_force_centering', true );
        $alignment = get_post_meta( $this->id, 'ultimeter_alignment', true );
        
        if ( $force_centering && !$alignment || !$force_centering && !$alignment ) {
            return 'center';
        } else {
            return $alignment;
        }
    
    }
    
    /**
     * Gets the style data for this Ultimeter.
     *
     * @return array
     */
    public function get_styles()
    {
        $styles = array(
            'primary_color'   => get_post_meta( $this->id, '_ultimeter_meter_color', true ),
            'secondary_color' => get_post_meta( $this->id, '_ultimeter_meter_outer_color', true ),
            'current_label'   => get_post_meta( $this->id, '_ultimeter_meter_progress_color', true ),
            'total_label'     => get_post_meta( $this->id, '_ultimeter_meter_goal_color', true ),
            'size'            => get_post_meta( $this->id, '_ultimeter_meter_size', true ),
            'alignment'       => $this->get_alignment(),
        );
        $this->styles = $styles;
        return $styles;
    }
    
    /**
     * Generate inline CSS to style our meters.
     *
     * @return false|string
     */
    public function get_inline_css()
    {
        $styles = $this->get_styles();
        $primary = ( !empty($styles['primary_color']) ? $styles['primary_color'] : '#F5D62F' );
        $secondary = ( !empty($styles['secondary_color']) ? $styles['secondary_color'] : '#eee' );
        $current = ( !empty($styles['current_label']) ? $styles['current_label'] : '' );
        $total = ( !empty($styles['total_label']) ? $styles['total_label'] : '' );
        ob_start();
        ?>
		#post-<?php 
        echo  esc_attr( $this->id ) ;
        ?> .primary-color { color: <?php 
        echo  esc_attr( $primary ) ;
        ?>; background-color: <?php 
        echo  esc_attr( $primary ) ;
        ?>; border-color: <?php 
        echo  esc_attr( $primary ) ;
        ?>; }
		#post-<?php 
        echo  esc_attr( $this->id ) ;
        ?> .secondary-color { color: <?php 
        echo  esc_attr( $secondary ) ;
        ?>; background-color: <?php 
        echo  esc_attr( $secondary ) ;
        ?>; border-color: <?php 
        echo  esc_attr( $secondary ) ;
        ?>; }
		#post-<?php 
        echo  esc_attr( $this->id ) ;
        ?> .current-label { color: <?php 
        echo  esc_attr( $current ) ;
        ?>; border-color: <?php 
        echo  esc_attr( $current ) ;
        ?>; }
		#post-<?php 
        echo  esc_attr( $this->id ) ;
        ?> .total-label { color: <?php 
        echo  esc_attr( $total ) ;
        ?>; border-color: <?php 
        echo  esc_attr( $total ) ;
        ?>; }
		#post-<?php 
        echo  esc_attr( $this->id ) ;
        ?> { justify-self: <?php 
        echo  esc_attr( $styles['alignment'] ) ;
        ?>; }
		<?php 
        return ob_get_clean();
    }
    
    /**
     * Gets the debug data for this Ultimeter.
     *
     * @return array
     */
    public function get_debug_data()
    {
        if ( get_post_meta( $this->id, 'ultimeter_debug_mode', true ) ) {
            return array(
                'enabled'       => get_post_meta( $this->id, 'ultimeter_debug_mode', true ),
                'debug_quietly' => get_post_meta( $this->id, 'ultimeter_debug_quiet_mode', true ),
            );
        }
    }
    
    /**
     * Gets an array of data for this Ultimeter.
     *
     * @return array
     */
    public function get_meter()
    {
        $meter = array(
            'id'          => $this->id,
            'name'        => $this->name,
            'type'        => $this->type,
            'format'      => $this->format,
            'language'    => $this->language,
            'currency'    => $this->currency,
            'output_type' => $this->output_type,
            'physics'     => $this->physics,
            'current'     => $this->current ?? $this->get_current(),
            'total'       => $this->total ?? $this->get_total(),
            'progress'    => $this->progress ?? $this->get_progress(),
            'styles'      => $this->get_styles(),
            'debug'       => $this->get_debug_data(),
        );
        return $meter;
    }
    
    /**
     * Renders our meter.
     *
     * @return false|string|void
     */
    public function render()
    {
        $meter = $this->get_meter();
        
        if ( $meter['debug'] ) {
            $hide = ( $meter['debug']['debug_quietly'] ? ' style="display: none;"' : '' );
            echo  '<pre' . $hide . '>' ;
            esc_html( print_r( $meter ) );
            echo  '</pre>' ;
        }
        
        if ( upgm_fs()->is_not_paying() ) {
            
            if ( 'thermometer' !== $meter['type'] && 'progressbar' !== $meter['type'] ) {
                if ( current_user_can( 'manage_options' ) ) {
                    echo  esc_html__( 'Invalid Ultimeter. Please check your configuration. Please check this Ultimeter\'s edit screen for error messages.', 'ultimeter' ) ;
                }
                return;
            }
        
        }
        
        if ( !$this->is_goalless && $meter['total'] <= 0 ) {
            if ( current_user_can( 'manage_options' ) ) {
                echo  esc_html__( 'Invalid Ultimeter. Please check your configuration. Please check this Ultimeter\'s edit screen for error messages.', 'ultimeter' ) ;
            }
            return;
        }
        
        $styles = $meter['styles'];
        $template = plugin_dir_path( __DIR__ ) . "templates/{$meter['type']}.php";
        
        if ( !file_exists( $template ) ) {
            if ( current_user_can( 'manage_options' ) ) {
                echo  esc_html__( 'Invalid Ultimeter. Please check your configuration. Please check this Ultimeter\'s edit screen for error messages.', 'ultimeter' ) ;
            }
            return;
        }
        
        $alignment = ( $styles['alignment'] ? 'ultimeter_align_' . $styles['alignment'] : '' );
        $size = $meter['styles']['size'] ?? 'medium';
        $style_pack = $styles['style_pack'] ?? 'none';
        ob_start();
        ?>
		<div id="post-<?php 
        echo  esc_attr( $meter['id'] ) ;
        ?>"
			 class="ultimeter-container <?php 
        echo  'sp-' . esc_attr( $style_pack ) . ' ' . esc_attr( $alignment ) . ' ' . esc_attr( $size ) . ' ' . esc_attr( $meter['type'] ) ;
        ?>"
			 data-current="<?php 
        echo  esc_attr( $meter['current'] ) ;
        ?>"
			 data-progress="<?php 
        echo  esc_attr( $meter['progress'] ) ;
        ?>"
			 data-meter-id="<?php 
        echo  esc_attr( $meter['id'] ) ;
        ?>"
			 data-meter="<?php 
        echo  htmlspecialchars( json_encode( $meter ) ) ;
        ?>">
			<?php 
        ?>
		<div class="ultimeter <?php 
        echo  esc_attr( $meter['type'] ) ;
        ?>">
			<?php 
        require $template;
        ?>
		</div>
		</div>

		<?php 
        return ob_get_clean();
    }

}