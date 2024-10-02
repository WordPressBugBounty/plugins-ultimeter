<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The class that describes an Ultimeter meter object.
 */
class Ultimeter_Meter_Type {

	/**
	 * Holds our meter sets.
	 *
	 * @var array
	 */
	private $files;

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
	 * @return array
	 */
	public function get_sets() {
		$sets = $this->sets;

		$sets['thermometers'] = array(
			'title' => 'Vertical Meters',
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
		$paths = (array) ultimeter_get_meter_paths();

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
	 * @return  array
	 */
	public function get_files() {
		return $this->files;
	}

	/**
	 * Gets an array of all meter types.
	 *
	 * @return array
	 */
	public function get_meter_types() {
		$files = $this->fetch();

		$meters = array();

		// Add each meter to the main array, grouped by set
		foreach ( $files as $key => $file ) {
			$meter = json_decode( file_get_contents( $file ), true );

			// If no order, set to 0
			if ( empty( $meter['order'] ) ) {
				$meter['order'] = 0;
			}

			$meters[ $meter['id'] ] = $meter;
		}

		return $meters;
	}

	/**
	 * Gets the type data for a given meter type.
	 *
	 * @param $type
	 *
	 * @return false|mixed
	 */
	public function get_meter_type( $type ) {
		if ( ! $type ) return false;

		$types = $this->get_meter_types();

		if ( array_key_exists( $type, $types ) ) {
			return $types[$type];
		}

		return false;
	}

	/**
	 * Gets an array of meter types, sorted into sets.
	 *
	 * @return array
	 */
	public function get_meter_types_by_set() {
		$files = $this->fetch();

		// Get the sets our meters will fit into
		$sets = $this->get_sets();

		$meters = array();

		// Add each meter to the main array, grouped by set
		foreach ( $files as $key => $file ) {
			$meter = json_decode( file_get_contents( $file ), true );

			// If no order, set to 0
			if ( empty( $meter['order'] ) ) {
				$meter['order'] = 0;
			}

			// If no set, set to 'uncategorised'
			if ( empty( $meter['set'] ) ) {
				$meter['set'] = 'uncategorised';
			}

			$meters[ $meter['set'] ][ $meter['id'] ] = $meter;
		}

		return $meters;
	}

	/**
	 * Gets an array of meter types, formatted for use in backend metabox.
	 *
	 * @return array
	 */
	public function get_CSF_meter_sets() {
		$meters = $this->get_meter_types_by_set();

		$csf_array = array();

		$sets = $this->get_sets();

		foreach ( $meters as $key => $value ) {
			$csf_item = array(
				'title' => $sets[ $key ]['title'],
				'order' => $sets[ $key ]['order'],
			);

			foreach ( $value as $set_item ) {
				$r = array(
					'id'      => $set_item['id'],
					'title'   => $set_item['name'],
					'order'   => $set_item['order'],
					'src'     => plugin_dir_url( __DIR__ ) . 'admin/assets/images/' . $set_item['image'],
					'premium' => $set_item['premium'],
				);

				$csf_item['items'][] = $r;
			}

			$csf_array[] = $csf_item;
		}

		return $csf_array;
	}
}
