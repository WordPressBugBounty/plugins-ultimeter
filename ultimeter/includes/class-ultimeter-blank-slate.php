<?php
/**
 * Ultimeter Blank Slate Class
 *
 * @package     Ultimeter
 * @copyright   Copyright (c) 2019, Ultimeter
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ultimeter_Blank_Slate {
	/**
	 * The current screen ID.
	 *
	 * @since  2.2
	 * @var string
	 */
	public $screen = '';

	/**
	 * Constructs the Ultimeter_Blank_Slate class.
	 *
	 * @since 2.2
	 */
	public function __construct() {
		$this->screen = get_current_screen()->id;
	}

	/**
	 * Initializes the class and hooks into WordPress.
	 *
	 * @since 2.2
	 */
	public function init() {
		// Bail early if screen cannot be detected.
		if ( empty( $this->screen ) ) {
			return null;
		}

		$args = array(
			'post_type' => 'ultimeter',
		);

		$loop = new WP_Query( $args );

		// Check we are on the Ultimeter list page.
		if ( 'edit-ultimeter' === $this->screen ) {
			if ( $loop->have_posts() ) {
				return false;
			}

			add_action( 'manage_posts_extra_tablenav', array( $this, 'render' ) );

			// Hide non-essential UI elements.
			add_action( 'admin_head', array( $this, 'hide_ui' ) );
		}
	}

	/**
	 * Renders the blank slate message.
	 *
	 * @since 2.2
	 *
	 * @param string $which The location of the list table hook: 'top' or 'bottom'.
	 */
	public function render( $which = 'bottom' ) {
		// Bail out to prevent content from rendering twice.
		if ( 'top' === $which ) {
			return null;
		}

		$template_path = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/blank-slate.php';

		include $template_path;
	}

	/**
	 * Hides non-essential UI elements when blank slate content is on screen.
	 *
	 * @since 2.2
	 */
	function hide_ui() {
		?>
		<style type="text/css">
		.search-box,
		.bulkactions,
		.subsubsub,
		.wp-list-table,
		.tablenav.top,
		.tablenav-pages {
			display: none;
		}
		</style>
		<?php
	}
}
