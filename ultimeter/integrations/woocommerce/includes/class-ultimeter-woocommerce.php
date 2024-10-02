<?php

class Ultimeter_WooCommerce {

	/**
	 * Our single instance.
	 */
	private static $instance;
	/**
	 * @var array
	 */
	private $products;

	/**
	 * Gets an instance.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	public function init() {
		add_filter( 'ultimeter_admin_goal_formats', array( $this, 'add_goal_format' ) );
		add_filter( 'ultimeter_admin_goal_options', array( $this, 'add_goal_options' ) );
	}

	public function add_goal_format( $formats ) {
		if ( upgm_fs()->is_plan( 'pro', true ) ) {
			$formats['woo'] = esc_html__( 'WooCommerce', 'ultimeter' );
		} elseif ( upgm_fs()->is_plan( 'enterprise', true ) ) {
			$formats['ultwoo'] = esc_html__( 'WooCommerce', 'ultimeter' );
		}

		return $formats;
	}

	public function add_goal_options( $options ) {
		if ( upgm_fs()->is_plan( 'pro', true ) ) {
			$options[] = array(
				'id'          => '_ultimeter_woocommerce',
				'type'        => 'select',
				'chosen'      => true,
				'placeholder' => esc_attr__( 'Select Product', 'ultimeter' ),
				'subtitle'    => '<img style="margin-top: 10px;" src="' . plugin_dir_url( __DIR__ ) . '/assets/images/woo.png' . '">',
				'desc'        => esc_html__(
					'Choose a product from the dropdown list.',
					'ultimeter'
				),
				'title'       => esc_html__( 'Product', 'ultimeter' ),
				'options'     => 'posts',
				'query_args'  => array(
					'post_type'      => array( 'product', 'product_variation' ),
					'posts_per_page' => - 1,
				),
				'dependency'  => array( '_ultimeter_goal_format', '==', 'woo' ),
			);

			$options[] = array(
				'id'         => '_ultimeter_woo_goal',
				'type'       => 'number',
				'desc'       => esc_html__( 'Enter your WooCommerce sales goal.', 'ultimeter' ),
				'title'      => esc_html__( 'Sales Goal', 'ultimeter' ),
				'dependency' => array( '_ultimeter_goal_format', '==', 'woo' ),
			);

			$options[] = array(
				'id'         => '_ultimeter_woo_upsell',
				'type'       => 'content',
				'class'      => 'ultimeter-upsell-container',
				'content'    => $this->woo_upsell(),
				'dependency' => array( '_ultimeter_goal_format', '==', 'woo' ),
			);
		} elseif ( upgm_fs()->is_plan( 'enterprise', true ) ) {
			$options[] = array(
				'id'         => '_ultimeter_ultwoo_metric',
				'type'       => 'radio',
				'default'    => 'sales_by_date',
				'subtitle'   => '<img style="margin-top: 10px;" src="' . plugin_dir_url( __DIR__ ) . '/assets/images/woo.png' . '">',
				'desc'       => esc_html__(
					'Choose the type of data you want the Ultimeter to track.',
					'ultimeter'
				),
				'title'      => esc_html__( 'Select a Report Type', 'ultimeter' ),
				'options'    => array(
					'sales_by_date'             => esc_html__( 'Total sales by date', 'ultimeter' ),
					'sales_by_date_net'         => esc_html__( 'Total sales by date (net)', 'ultimeter' ),
					'sales_by_product'          => esc_html__( 'Sales by product', 'ultimeter' ),
					'sales_by_product_less_tax' => esc_html__( 'Sales by product (less tax)', 'ultimeter' ),
					'units_by_date'             => esc_html__( 'Units by date', 'ultimeter' ),
					'units_by_product'          => esc_html__( 'Units by product', 'ultimeter' ),
				),
				'dependency' => array( '_ultimeter_goal_format', '==', 'ultwoo' ),
			);

			$options[] = array(
				'id'         => '_ultimeter_ultwoo_time',
				'type'       => 'radio',
				'default'    => 'all_time',
				'desc'       => esc_html__(
					'Choose a time-range to see data for.',
					'ultimeter'
				),
				'title'      => esc_html__( 'Time Range', 'ultimeter' ),
				'options'    => array(
					'all_time'   => esc_html__( 'All time', 'ultimeter' ),
					'year'       => esc_html__( 'This Year', 'ultimeter' ),
					'last_month' => esc_html__( 'Last month', 'ultimeter' ),
					'month'      => esc_html__( 'This month', 'ultimeter' ),
					'7day'       => esc_html__( 'Last 7 days', 'ultimeter' ),
					'custom'     => esc_html__( 'Custom', 'ultimeter' ),
				),
				'dependency' => array( '_ultimeter_goal_format', '==', 'ultwoo' ),
			);

			$options[] = array(
				'id'         => '_ultimeter_ultwoo_time_custom_range',
				'type'       => 'date',
				'from_to'    => true,
				'title'      => esc_html__( 'Custom Date Range', 'ultimeter' ),
				'dependency' => array(
					array( '_ultimeter_goal_format', '==', 'ultwoo' ),
					array( '_ultimeter_ultwoo_time', '==', 'custom' ),
				),
			);

			$options[] = array(
				'id'         => '_ultimeter_ultwoo_goal',
				'type'       => 'number',
				'desc'       => esc_html__( 'Enter your WooCommerce sales goal.', 'ultimeter' ),
				'title'      => esc_html__( 'Sales Goal', 'ultimeter' ),
				'dependency' => array( '_ultimeter_goal_format', '==', 'ultwoo' ),
			);

			$options[] = array(
				'id'          => '_ultimeter_ultwoo_product',
				'type'        => 'select',
				'chosen'      => true,
				'multiple'    => true,
				'placeholder' => esc_attr__( 'Select Products', 'ultimeter' ),
				'desc'        => esc_html__(
					'Choose a product or products from the dropdown list.',
					'ultimeter'
				),
				'title'       => esc_html__( 'Products', 'ultimeter' ),
				'options'     => 'posts',
				'query_args'  => array(
					'post_type'      => array( 'product', 'product_variation' ),
					'posts_per_page' => - 1,
				),
				'dependency'  => array( '_ultimeter_goal_format', '==', 'ultwoo' ),
			);

			$options[] = array(
				'id'         => '_ultimeter_ultwoo_custom_unit',
				'type'       => 'text',
				'desc'       => esc_html__(
					'Enter the name to display for your product. This could be the product name, or a simpler form, such as box or crate.',
					'ultimeter'
				),
				'title'      => esc_html__( 'Product Description', 'ultimeter' ),
				'dependency' => array( '_ultimeter_goal_format', '==', 'ultwoo' ),
			);

			$options[] = array(
				'id'         => '_ultimeter_ultwoo_modifier',
				'type'       => 'number',
				'desc'       => esc_html__(
					'Allows you to show that you give a percentage of profits to charity. For example, for ten percent of sales of a product, enter \'10\'. If you are match-funding your sales, enter \'200\' into the box, so the Ultimeter displays twice the value.',
					'ultimeter'
				),
				'title'      => esc_html__( 'Percentage Modifier', 'ultimeter' ),
				'dependency' => array( '_ultimeter_goal_format', '==', 'ultwoo' ),
			);

			$options[] = array(
				'id'         => '_ultimeter_boost_ultwoo',
				'type'       => 'number',
				'desc'       => esc_html__(
					'Boost your WooCommerce amount by the figure here. So to boost your total sales by $1000, just enter 1000.',
					'ultimeter'
				),
				'title'      => esc_html__( 'Boost', 'ultimeter' ),
				'dependency' => array( '_ultimeter_goal_format', '==', 'ultwoo' ),
			);
		}

		return $options;
	}

	/**
	 * Renders an upsell container.
	 *
	 * @return false|string
	 */
	public function woo_upsell() {
		ob_start(); ?>

		<div class="ultimeter-upsell">
			<div class="ultimeter-upsell-left">
				<h2>Using WooCommerce? Supercharge Your Meter With The Enterprise Version!</h2>
				<p>Track more than one product, or decide between total sales, or units sold. Or, track the total sales
					of your entire store!</p>
				<p>Choose different date ranges, including all-time, this year, this month, this week, or a custom
					range.</p>
				<p>Do you give 10% of your profits to charity? Do you run a match funding scheme? Ultimeter can make
					that happen, by allowing you to display a percentage of the tracked amount.</p>
				<p>Sometimes you don't ever want your campaign to start at zero. Or maybe you need to take in cash sales
					from your physical store. Ultimeter can boost the tracked figure by an amount you choose.</p>
				<a class="ultimeter-upsell-button button" href="<?php echo upgm_fs()->get_upgrade_url(); ?>">Upgrade
					Now</a>
			</div>
			<div class="ultimeter-upsell-right">
				<img alt="WooCommerce Integration logo" src="<?php echo plugin_dir_url( __DIR__ ) . '../../admin/assets/images/woo-upsell.png'; ?>">
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

}

$ultimeter_woocommerce = Ultimeter_WooCommerce::get_instance();
