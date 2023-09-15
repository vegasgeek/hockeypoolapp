<?php
/**
 * Plugin Name: HockeyPool Manager
 * Plugin URI: https://hockeypool.app
 * Description: Tools for running HockeyPool.app
 * Author: John Hawkins
 * Version: 1.0
 * Author URI: https://hockeypool.app
 * Text Domain: hockeypool
 *
 * @HockeyPool
 */

DEFINE( 'VG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
DEFINE( 'VG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once 'setup.php';
require_once 'admin-tools.php';
require_once 'acf-prefills.php';
require_once 'scoring.php';


function vg_setup_new_user( $user_id ) {
	$player  = get_userdata( $user_id );
	$display = $player->display_name;

	$args = array(
		'post_type'   => 'picks',
		'post_status' => 'publish',
		'post_author' => $user_id,
		'post_title'  => $display,
	);

	$post_id = wp_insert_post( $args );
	update_field( 'player', $user_id, $post_id );
	update_field( 'total_score', 0, $post_id );
	update_field( 'r1_score', 0, $post_id );
	update_field( 'r2_score', 0, $post_id );
	update_field( 'r3_score', 0, $post_id );
	update_field( 'r4_score', 0, $post_id );

	$cur_year = wp_date( 'Y' );
	$taxonomy = 'playoff_year';
	wp_set_object_terms( $post_id, $cur_year, $taxonomy );

}
add_action( 'new_user_approve_approve_user', 'vg_setup_new_user', 10, 1 );
