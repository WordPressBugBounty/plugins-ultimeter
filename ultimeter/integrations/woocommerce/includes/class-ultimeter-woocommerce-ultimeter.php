<?php

/**
 * A WooCommerce based Ultimeter.
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Class that extends our meter class, for WooCommerce specific functionality.
 */
class Ultimeter_WooCommerce_Ultimeter extends Ultimeter_Ultimeter
{
    /**
     * Required files and hooks.
     *
     * @return void
     */
    public function init()
    {
        if ( class_exists( 'woocommerce' ) ) {
            // Include the main WooCommerce report class.
            include_once WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php';
        }
    }
    
    /**
     * Get the product(s) associated with this Ultimeter.
     *
     * @return string
     */
    public function get_products()
    {
        return get_post_meta( $this->id, '_ultimeter_woocommerce', true );
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
        $current = $this->get_sales_by_product( $this->get_products() );
        // Fallback to the default.
        if ( !$current ) {
            $current = $default;
        }
        // Clean any unwanted commas.
        $current = (int) str_replace( ',', '', $current );
        $this->current = $current;
        return $current;
    }
    
    /**
     * Get the total for this Ultimeter.
     *
     * @return int
     */
    public function get_total()
    {
        // Set a default.
        $default = apply_filters( 'ultimeter_default_total', 100 );
        $total = get_post_meta( $this->id, '_ultimeter_woo_goal', true );
        // If we still can't find a total, fallback to the goal amount entry, and finally the default.
        if ( !$total ) {
            $total = get_post_meta( $this->id, '_ultimeter_goal_amount', true ) ?? $default;
        }
        // Clean any unwanted commas.
        $total = (int) str_replace( ',', '', $total );
        $this->total = $total;
        return $total;
    }
    
    /**
     * Get WooCommerce sales data.
     *
     * @param int $product The WooCommerce product ID.
     *
     * @return array
     */
    public function get_sales_by_product( $product )
    {
        if ( empty($product) || !class_exists( 'woocommerce' ) ) {
            return 0;
        }
        // Create a new WC_Admin_Report object
        include_once WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php';
        $wc_report = new WC_Admin_Report();
        $where_meta = array();
        $where_meta[] = array(
            'type'       => 'order_item_meta',
            'meta_key'   => '_product_id',
            'operator'   => 'in',
            'meta_value' => $product,
        );
        // Based on woocoommerce/includes/admin/reports/class-wc-report-sales-by-product.php.
        $gross = $wc_report->get_order_report_data( array(
            'data'       => array(
            '_line_subtotal' => array(
            'type'            => 'order_item_meta',
            'order_item_type' => 'line_item',
            'function'        => 'SUM',
            'name'            => 'gross',
        ),
        ),
            'query_type' => 'get_var',
            'where_meta' => $where_meta,
        ) );
        
        if ( $gross > 0 ) {
            return $gross;
        } else {
            return 0;
        }
    
    }
    
    /**
     * The output type controls how the values are rendered on the front end. It should always be one of 3 types:
     * ultimeter_currency, ultimeter_percentage, or ultimeter_custom.
     *
     * @return string
     */
    public function get_output_type()
    {
        $output_type = 'ultimeter_currency';
        return $output_type;
    }
    
    /**
     * Get the current range and calculate the start and end dates.
     *
     * @return array|false
     */
    public function calculate_current_range()
    {
        $current_range = get_post_meta( $this->id, '_ultimeter_ultwoo_time', true );
        if ( empty($current_range) || 'all_time' === $current_range ) {
            return false;
        }
        switch ( $current_range ) {
            case 'custom':
                $from_to = get_post_meta( $this->id, '_ultimeter_ultwoo_time_custom_range', true );
                $start = sanitize_text_field( get_post_meta( $this->id, '_ultimeter_ultwoo_time_start_date', true ) );
                $end = sanitize_text_field( get_post_meta( $this->id, '_ultimeter_ultwoo_time_end_date', true ) );
                
                if ( isset( $from_to ) ) {
                    $start = $from_to['from'];
                    $end = $from_to['to'];
                    $start_date = strtotime( $start );
                    
                    if ( empty($end) ) {
                        $end_date = strtotime( 'midnight' );
                    } else {
                        $end_date = strtotime( 'midnight', strtotime( $end ) );
                    }
                
                } elseif ( isset( $start ) && isset( $end ) ) {
                    $start_date = strtotime( $start );
                    
                    if ( empty($end) ) {
                        $end_date = strtotime( 'midnight' );
                    } else {
                        $end_date = strtotime( 'midnight', strtotime( $end ) );
                    }
                
                } else {
                    return false;
                }
                
                break;
            case 'year':
                $start_date = strtotime( gmdate( 'Y-01-01' ) );
                $end_date = strtotime( 'now' );
                break;
            case 'last_month':
                $first_day_current_month = strtotime( gmdate( 'Y-m-01' ) );
                $start_date = strtotime( gmdate( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
                $end_date = strtotime( gmdate( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
                break;
            case 'month':
                $start_date = strtotime( gmdate( 'Y-m-01' ) );
                $end_date = strtotime( 'now' );
                break;
            case '7day':
                $start_date = strtotime( '-6 days', strtotime( 'midnight' ) );
                $end_date = strtotime( 'now' );
                break;
        }
        return array(
            'start_date' => $start_date,
            'end_date'   => $end_date,
        );
    }

}