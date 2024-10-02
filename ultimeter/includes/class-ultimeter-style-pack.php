<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The class that describes an Ultimeter style pack object.
 */
class Ultimeter_Style_Pack {

	/**
	 * Holds our meter style packs.
	 *
	 * @var array
	 */
	private $files;

	/**
	 * The found JSON Ultimeter style packs.
	 *
	 * @var array
	 */
	private $meters = array();

	/**
	 * The found JSON Ultimeter meter sets.
	 *
	 * @var array
	 */
	private $sets = array();

	private static $instance;

	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Fetch latest meters
		$this->get_sets();
		$this->fetch();
	}

	/**
	 * Build our array of possible meter sets.
	 *
	 * @return void
	 */
	public function get_sets() {
		$sets = $this->sets;

		$sets['thermometers'] = array(
			'title' => 'Thermometers',
			'order' => 0,
		);

		$sets['progress_bars'] = array(
			'title' => 'Progress Bars',
			'order' => 1,
		);

		$sets['radial_meters'] = array(
			'title' => 'Radial Meters',
			'order' => 2,
		);

		$sets['goalless'] = array(
			'title' => 'Goalless Meters',
			'order' => 3,
		);

		$sets['custom'] = array(
			'title' => 'Build Your Own Meter',
			'order' => 4,
		);

		$sets['uncategorized'] = array(
			'title' => 'Other Meters',
			'order' => 5,
		);

		// Store data and return.
		$this->sets = $sets;

		return $sets;
	}

	/**
	 * Scans for Ultimeter meter files, and builds a keyed array.
	 *
	 * @return array
	 */
	public function fetch() {
		$json_files = array();

		// Loop over our paths and parse JSON files.
		$paths = (array) ultimeter_get_style_pack_paths();

		foreach ( $paths as $path ) {
			if ( is_dir( $path ) ) {
				$files = scandir( $path );
				if ( $files ) {
					foreach ( $files as $filename ) {
						// Ignore hidden files.
						if ( $filename[0] === '.' ) {
							continue;
						}

						// Ignore sub directories.
						$file = untrailingslashit( $path ) . '/' . $filename;
						if ( is_dir( $file ) ) {
							continue;
						}

						// Ignore non JSON files.
						$ext = pathinfo( $filename, PATHINFO_EXTENSION );
						if ( $ext !== 'json' ) {
							continue;
						}

						// Read JSON data.
						$json = json_decode( file_get_contents( $file ), true );

						if ( ! is_array( $json ) || ! isset( $json['id'] ) ) {
							continue;
						}

						// Append data.
						$json_files[ $json['id'] ] = $file;
					}
				}
			}
		}

		// Store data and return.
		$this->files = $json_files;

		return $json_files;
	}

	/**
	 * Returns an array of found JSON Ultimeter meter files.
	 *
	 * @param void
	 *
	 * @return  array
	 */
	public function get_files() {
		return $this->files;
	}

	/**
	 * Gets an array of all meter style packs.
	 *
	 * @return array
	 */
	public function get_style_packs() {
		$files = $this->fetch();

		$packs = array();

		// Add each meter to the main array, grouped by set
		foreach ( $files as $key => $file ) {
			$pack = json_decode( file_get_contents( $file ), true );

			$packs[ $pack['id'] ] = $pack;
		}

		return $packs;
	}

	/**
	 * Gets the style pack information for a given style pack ID.
	 *
	 * @param $type
	 *
	 * @return false|mixed
	 */
	public function get_style_pack( $pack_id ) {
		if ( ! $pack_id ) return false;

		$packs = $this->get_style_packs();

		if ( array_key_exists( $pack_id, $packs ) ) {
			return $packs[$pack_id];
		}

		return false;
	}

	/**
	 * Gets an array of style packs, formatted for use in backend metabox.
	 *
	 * @return array
	 */
	public function get_CSF_style_packs() {
		$packs = $this->get_style_packs();

		$csf_array = array();

		foreach ( $packs as $pack ) {
			$csf_item = array(
				'id' => $pack['id'],
				'title' => $pack['title'],
				'order' => $pack['order'] ?? 0,
				'src'     => plugin_dir_url( __DIR__ ) . 'admin/assets/images/' . $pack['image'],
				'data' => $pack,
			);

			$csf_array[ $pack['id'] ] = $csf_item;
		}

		return $csf_array;
	}
}
