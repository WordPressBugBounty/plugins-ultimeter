<?php
/**
 * Manage Ultimeter Gutenberg Block
 * PHP version 7
 *
 * @category   Class
 * @package    Ultimeter Gutenberg
 * @author     Chandni Patel
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 * @link       http://phpwebdev.in/
 * @since      2.4.0
 */

if ( ! class_exists( 'Ultimeter_Gutenberg_Block' ) ) {
	/**
	 * Class to manage Ultimeter Gutenberg Block
	 */
	class Ultimeter_Gutenberg_Block {

		/**
		 * Initialize the class and set its properties.
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Decoupler.
		 *
		 * @return void
		 */
		public function init() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Register the JavaScript And Stylesheet for the Ultimeter Gutenberg Block.
		 *
		 * @since    2.4.0
		 */
		public function enqueue_scripts() {
			/**
			* The class responsible for defining all actions that occur for the Ultimeter Gutenberg Block.
			*/
			$ultimeter_obj = new Ultimeter();

			if ( is_admin() ) {
				// ultimeter-gutenberg-block script for Block.
				wp_enqueue_script( 'ultimeter-gutenberg-block', plugin_dir_url( __FILE__ ) . 'assets/js/ultimeter-gutenberg-block.js', array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor', 'wp-compose' ), ULTIMETER_VERSION, false );

				// ultimeter-gutenberg-block style for Block.
				wp_enqueue_style( 'ultimeter-gutenberg-block', plugin_dir_url( __FILE__ ) . 'assets/css/ultimeter-gutenberg-block.css', array( 'wp-editor' ), ULTIMETER_VERSION, 'all' );
			}

			register_block_type(
				'ultimeter-gutenberg-block/shortcode-gutenberg',
				array(
					'editor_script'   => 'ultimeter-gutenberg-block',
					'render_callback' => array( $ultimeter_obj, 'create_shortcode' ),
					'attributes'      => array(
						'id' => array(
							'type' => 'string',
						),
					),
				)
			);

			/* Define plugin logo image url global variable */
			wp_localize_script(
				'ultimeter-gutenberg-block',
				'ultimeterGlobal',
				array(
					'logoUrl' => plugin_dir_url( __DIR__ ) . 'admin/assets/images/ultimeter.png',
				)
			);

		}
	}
}
$plugin_gutenberg_block = new Ultimeter_Gutenberg_Block();
