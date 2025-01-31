<?php

/**
 * A WooCommerce based Ultimeter with HPOS compatibility.
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Class that extends our meter class, for WooCommerce-specific functionality.
 */
class Ultimeter_WooCommerce_Ultimeter extends Ultimeter_Ultimeter {
    /**
     * Required files and hooks.
     *
     * @return void
     */
    public function init() {
    }

    /**
     * Get the product(s) associated with this Ultimeter.
     *
     * @return string
     */
    public function get_products() {
        return get_post_meta( $this->id, '_ultimeter_woocommerce', true );
    }

    /**
     * Get the current (raised) value for this Ultimeter.
     *
     * @return int
     */
    /**
     * Get the current (raised) value for this Ultimeter.
     *
     * @return int
     */
    public function get_current() {
        // Set a default.
        $default = apply_filters( 'ultimeter_default_current', 0 );
        $current = $this->get_sales_by_product( $this->get_products() );
        return ( $current ?: $default );
    }

    /**
     * Apply boost and percentage modifiers.
     *
     * @param float|int $current The current value.
     * @return float|int
     */
    private function apply_modifiers( $current ) {
        return $current;
    }

    /**
     * Get the total for this Ultimeter.
     *
     * @return int
     */
    public function get_total() {
        $default = apply_filters( 'ultimeter_default_total', 100 );
        $total = get_post_meta( $this->id, '_ultimeter_woo_goal', true );
        return (int) str_replace( ',', '', ( $total ?: $default ) );
    }

    /**
     * Get WooCommerce sales data.
     *
     * @param int $product The WooCommerce product ID.
     *
     * @return array
     */
    public function get_sales_by_product( $product ) {
        if ( empty( $product ) || !class_exists( 'woocommerce' ) ) {
            return 0;
        }
        $args = array(
            'status'  => array(
                'completed',
                'processing',
                'on-hold',
                'refunded'
            ),
            'limit'   => -1,
            'orderby' => 'date',
            'order'   => 'DESC',
        );
        $orders = wc_get_orders( $args );
        $sales = 0;
        foreach ( $orders as $order ) {
            foreach ( $order->get_items() as $item ) {
                // Compare the item product ID directly with the supplied product ID.
                if ( $item->get_product_id() === (int) $product ) {
                    $sales += $item->get_total();
                }
            }
        }
        return $sales;
    }

    /**
     * The output type controls how the values are rendered on the front end.
     *
     * @return string
     */
    public function get_output_type() {
        return 'ultimeter_currency';
    }

    /**
     * Get the current range and calculate the start and end dates.
     *
     * @return array|false
     */
    public function calculate_current_range() {
        $current_range = get_post_meta( $this->id, '_ultimeter_ultwoo_time', true );
        if ( empty( $current_range ) || 'all_time' === $current_range ) {
            return false;
            // No date filtering.
        }
        switch ( $current_range ) {
            case 'custom':
                $from_to = get_post_meta( $this->id, '_ultimeter_ultwoo_time_custom_range', true );
                $start = sanitize_text_field( get_post_meta( $this->id, '_ultimeter_ultwoo_time_start_date', true ) );
                $end = sanitize_text_field( get_post_meta( $this->id, '_ultimeter_ultwoo_time_end_date', true ) );
                // Handle custom ranges.
                if ( isset( $from_to['from'], $from_to['to'] ) ) {
                    $start_date = strtotime( $from_to['from'] );
                    $end_date = strtotime( $from_to['to'] );
                    return gmdate( 'Y-m-d H:i:s', $start_date ) . '...' . gmdate( 'Y-m-d H:i:s', $end_date );
                } elseif ( isset( $start ) && isset( $end ) ) {
                    $start_date = strtotime( $start );
                    $end_date = strtotime( $end );
                    return gmdate( 'Y-m-d H:i:s', $start_date ) . '...' . gmdate( 'Y-m-d H:i:s', $end_date );
                } elseif ( isset( $start ) ) {
                    // Handle "after" queries.
                    $start_date = strtotime( $start );
                    return '>=' . gmdate( 'Y-m-d H:i:s', $start_date );
                } elseif ( isset( $end ) ) {
                    // Handle "before" queries.
                    $end_date = strtotime( $end );
                    return '<=' . gmdate( 'Y-m-d H:i:s', $end_date );
                }
                break;
            case 'year':
                // Yearly range.
                $start_date = strtotime( gmdate( 'Y-01-01' ) );
                $end_date = strtotime( 'now' );
                return gmdate( 'Y-m-d H:i:s', $start_date ) . '...' . gmdate( 'Y-m-d H:i:s', $end_date );
            case 'last_month':
                // Last month's range.
                $start_date = strtotime( 'first day of last month' );
                $end_date = strtotime( 'last day of last month' );
                return gmdate( 'Y-m-d H:i:s', $start_date ) . '...' . gmdate( 'Y-m-d H:i:s', $end_date );
            case 'month':
                // Current month's range.
                $start_date = strtotime( gmdate( 'Y-m-01' ) );
                $end_date = strtotime( 'now' );
                return gmdate( 'Y-m-d H:i:s', $start_date ) . '...' . gmdate( 'Y-m-d H:i:s', $end_date );
            case '7day':
                // Last 7 days range.
                $start_date = strtotime( '-6 days' );
                $end_date = strtotime( 'now' );
                return gmdate( 'Y-m-d H:i:s', $start_date ) . '...' . gmdate( 'Y-m-d H:i:s', $end_date );
            default:
                return false;
        }
    }

}
