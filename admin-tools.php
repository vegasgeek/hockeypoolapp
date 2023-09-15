<?php
/**
 * Admin tools
 *
 * @package HockeyPool
 */

/**
 * On save, move winners forward to next round.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function vg_move_winners_forward( $post_id ) {
	if ( 'playoffs' !== get_post_type( $post_id ) ) {
		return;
	}

	$cur_year = vg_get_cur_year( $post_id );

	// Round 1.
	$winner_w_1_1 = get_field( 'matchup_w_1_1_winner', $post_id );
	$winner_w_1_2 = get_field( 'matchup_w_1_2_winner', $post_id );
	$winner_w_1_3 = get_field( 'matchup_w_1_3_winner', $post_id );
	$winner_w_1_4 = get_field( 'matchup_w_1_4_winner', $post_id );
	$winner_e_1_1 = get_field( 'matchup_e_1_1_winner', $post_id );
	$winner_e_1_2 = get_field( 'matchup_e_1_2_winner', $post_id );
	$winner_e_1_3 = get_field( 'matchup_e_1_3_winner', $post_id );
	$winner_e_1_4 = get_field( 'matchup_e_1_4_winner', $post_id );

	if ( $winner_w_1_1 ) {
		$data = array(
			'team_1' => $winner_w_1_1,
		);
		update_field( 'matchup_w_2_1', $data, $post_id );
	}

	if ( $winner_w_1_2 ) {
		$data = array(
			'team_2' => $winner_w_1_2,
		);
		update_field( 'matchup_w_2_1', $data, $post_id );
	}

	if ( $winner_w_1_3 ) {
		$data = array(
			'team_1' => $winner_w_1_3,
		);
		update_field( 'matchup_w_2_2', $data, $post_id );
	}

	if ( $winner_w_1_4 ) {
		$data = array(
			'team_2' => $winner_w_1_4,
		);
		update_field( 'matchup_w_2_2', $data, $post_id );
	}

	if ( $winner_e_1_1 ) {
		$data = array(
			'team_1' => $winner_e_1_1,
		);
		update_field( 'matchup_e_2_1', $data, $post_id );
	}

	if ( $winner_e_1_2 ) {
		$data = array(
			'team_2' => $winner_e_1_2,
		);
		update_field( 'matchup_e_2_1', $data, $post_id );
	}

	if ( $winner_e_1_3 ) {
		$data = array(
			'team_1' => $winner_e_1_3,
		);
		update_field( 'matchup_e_2_2', $data, $post_id );
	}

	if ( $winner_e_1_4 ) {
		$data = array(
			'team_2' => $winner_e_1_4,
		);
		update_field( 'matchup_e_2_2', $data, $post_id );
	}

	// Round 2.
	$winner_w_2_1 = get_field( 'matchup_w_2_1_winner', $post_id );
	$winner_w_2_2 = get_field( 'matchup_w_2_2_winner', $post_id );
	$winner_e_2_1 = get_field( 'matchup_e_2_1_winner', $post_id );
	$winner_e_2_2 = get_field( 'matchup_e_2_2_winner', $post_id );

	if ( $winner_w_2_1 ) {
		$data = array(
			'team_1' => $winner_w_2_1,
		);
		update_field( 'matchup_w_3_1', $data, $post_id );
	}

	if ( $winner_w_2_2 ) {
		$data = array(
			'team_2' => $winner_w_2_2,
		);
		update_field( 'matchup_w_3_1', $data, $post_id );
	}

	if ( $winner_e_2_1 ) {
		$data = array(
			'team_1' => $winner_e_2_1,
		);
		update_field( 'matchup_e_3_1', $data, $post_id );
	}

	if ( $winner_e_2_2 ) {
		$data = array(
			'team_2' => $winner_e_2_2,
		);
		update_field( 'matchup_e_3_1', $data, $post_id );
	}

	// Round 3.
	$winner_w_3_1 = get_field( 'matchup_w_3_1_winner', $post_id );
	$winner_e_3_1 = get_field( 'matchup_e_3_1_winner', $post_id );

	if ( $winner_w_3_1 ) {
		$data = array(
			'team_1' => $winner_w_3_1,
		);
		update_field( 'matchup_stanley_cup', $data, $post_id );
	}

	if ( $winner_e_3_1 ) {
		$data = array(
			'team_2' => $winner_e_3_1,
		);
		update_field( 'matchup_stanley_cup', $data, $post_id );
	}

	vg_recalculate_player_points( $cur_year, $post_id );
}
add_action( 'save_post', 'vg_move_winners_forward', 11 );

/**
 * Get year of post.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function vg_get_cur_year( $post_id ) {
	$cur_year = get_the_terms( $post_id, 'playoff_year' );

	return $cur_year[0]->slug;
}
