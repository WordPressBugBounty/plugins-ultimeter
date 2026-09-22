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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
			add_action( 'init', array( $this, 'register_block' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		}

		/**
		 * Register the block type and its editor assets.
		 *
		 * Must run on init so the block exists before block themes render
		 * template HTML (which happens before wp_head / wp_enqueue_scripts).
		 *
		 * @since 2.4.0
		 */
		public function register_block() {
			$script_deps = array(
				'wp-i18n',
				'wp-element',
				'wp-blocks',
				'wp-components',
				'wp-data',
				'wp-server-side-render',
			);

			wp_register_script(
				'ultimeter-gutenberg-block',
				plugin_dir_url( __FILE__ ) . 'assets/js/ultimeter-gutenberg-block.js',
				$script_deps,
				ULTIMETER_VERSION,
				true
			);

			wp_localize_script(
				'ultimeter-gutenberg-block',
				'ultimeterGlobal',
				array(
					'logoUrl' => plugin_dir_url( __DIR__ ) . 'admin/assets/images/logo.svg',
				)
			);

			wp_register_style(
				'ultimeter-gutenberg-block',
				plugin_dir_url( __FILE__ ) . 'assets/css/ultimeter-gutenberg-block.css',
				array( 'wp-edit-blocks' ),
				ULTIMETER_VERSION
			);

			register_block_type(
				'ultimeter-gutenberg-block/shortcode-gutenberg',
				array(
					'api_version'     => 2,
					'editor_script'   => 'ultimeter-gutenberg-block',
					'editor_style'    => 'ultimeter-gutenberg-block',
					'render_callback' => array( $this, 'render_block' ),
					'attributes'      => array(
						'id' => array(
							'type'    => 'string',
							'default' => '',
						),
					),
				)
			);
		}

		/**
		 * Enqueue editor-only assets.
		 *
		 * @since 3.0.9
		 */
		public function enqueue_editor_assets() {
			wp_enqueue_script( 'ultimeter-gutenberg-block' );
			wp_enqueue_style( 'ultimeter-gutenberg-block' );
		}

		/**
		 * Server-side render for the block. Same output as the shortcode.
		 *
		 * @param array $attributes Block attributes.
		 * @return string
		 */
		public function render_block( $attributes ) {
			$id = isset( $attributes['id'] ) ? absint( $attributes['id'] ) : 0;

			if ( ! $id ) {
				return '';
			}

			return do_shortcode( sprintf( '[ultimeter id="%d"]', $id ) );
		}
	}
}
$ultimeter_gutenberg_block = new Ultimeter_Gutenberg_Block();
