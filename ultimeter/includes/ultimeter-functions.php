<?php

/**
 * Functions used by Ultimeter across the plugin.
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Get an array of paths where meter JSON files are stored.
 *
 * @return mixed|void
 */
function ultimeter_get_meter_paths()
{
    $paths = array( plugin_dir_path( __DIR__ ) . 'json/meters' );
    return apply_filters( 'ultimeter_get_meter_paths', $paths );
}

/**
 * Simple function to get the format for a given Ultimeter. We use this to determine which Ultimeter class to instantiate.
 *
 * @param string $id ID of an existing Ultimeter.
 *
 * @return mixed
 */
function ultimeter_get_format( $id )
{
    return get_post_meta( $id, '_ultimeter_goal_format', true );
}

/**
 * Get an array of paths where style pack JSON files are stored.
 *
 * @return mixed|void
 */
function ultimeter_get_style_pack_paths()
{
    $paths = array( plugin_dir_path( __DIR__ ) . 'json/style-packs' );
    return apply_filters( 'ultimeter_get_style_pack_paths', $paths );
}

/**
 * Gets an array of ISO-639-1 language codes.
 *
 * @see https://gist.github.com/jrnk/8eb57b065ea0b098d571
 *
 * @return mixed|null
 */
function ultimeter_get_languages()
{
    $file = plugin_dir_path( __DIR__ ) . 'json/languages.json';
    return json_decode( file_get_contents( $file ), true );
}

/**
 * Gets an array of ISO 4217 country codes.
 *
 * @see https://github.com/ourworldincode/currency
 *
 * @return array|bool|null
 */
function ultimeter_get_currencies()
{
    $file = plugin_dir_path( __DIR__ ) . 'json/currencies.json';
    $currencies = json_decode( file_get_contents( $file ), true );
    return array_combine( array_keys( $currencies ), array_column( $currencies, 'name' ) );
}
