<?php
/**
 * Setup functions
 *
 * @package HockeyPool
 */

/**
 * Redirect ACF to save JSON to our plugin folder
 *
 * @param string $path Path to save JSON file.
 */
function vg_acf_json_save_point( $path ) {
	// update path.
	$path = VG_PLUGIN_PATH . '/acf-json';

	// return.
	return $path;
}
add_filter( 'acf/settings/save_json', 'vg_acf_json_save_point' );

/**
 * Tell ACF to load JSON files from our plugin folder
 *
 * @param string $paths Path to load JSON file.
 */
function vg_acf_json_load_point( $paths ) {
	// remove original path.
	unset( $paths[0] );

	// append path.
	$paths[] = VG_PLUGIN_PATH . '/acf-json';

	// return.
	return $paths;
}
add_filter( 'acf/settings/load_json', 'vg_acf_json_load_point' );

/**
 * Register custom blocks.
 *
 * @return void
 */
function vg_register_blocks() {
	register_block_type( VG_PLUGIN_PATH . '/blocks/stats-grid/block.json' );
	register_block_type( VG_PLUGIN_PATH . '/blocks/picks-header/block.json' );
	register_block_type( VG_PLUGIN_PATH . '/blocks/picks-teams/block.json' );
	register_block_type( VG_PLUGIN_PATH . '/blocks/picks-form/block.json' );
}
add_action( 'init', 'vg_register_blocks' );
