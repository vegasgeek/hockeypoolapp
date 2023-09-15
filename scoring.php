<?php
/**
 * Scoring tools
 *
 * @package HockeyPool
 */

/**
 * Recalculate player points.
 *
 * @param int $cur_year Current year.
 * @param int $playoff_id Playoff ID.
 *
 * @return void
 */
function vg_recalculate_player_points( $cur_year, $playoff_id ) {
	// Grab all the playoff data.
	$playoff_meta = get_fields( $playoff_id );

	// Select all Picks for this year.
	$args  = array(
		'post_type'      => 'picks',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'playoff_year',
				'field'    => 'slug',
				'terms'    => $cur_year,
			),
		),
	);
	$picks = get_posts( $args );

	foreach ( $picks as $pick_id ) {

		$pick_meta = get_fields( $pick_id );

		// Matchup Arrays.
		$matchups['r1'] = array( 'w_1_1', 'w_1_2', 'w_1_3', 'w_1_4', 'e_1_1', 'e_1_2', 'e_1_3', 'e_1_4' );
		$matchups['r2'] = array( 'w_2_1', 'w_2_2', 'e_2_1', 'e_2_2' );
		$matchups['r3'] = array( 'w_3_1', 'e_3_1' );
		$matchups['r4'] = array( 'stanley_cup' );

		$scores                 = array();
		$scores['r1']           = array();
		$scores['r1']['team']   = 0;
		$scores['r1']['win_in'] = 0;
		$scores['r2']           = array();
		$scores['r2']['team']   = 0;
		$scores['r2']['win_in'] = 0;
		$scores['r3']           = array();
		$scores['r3']['team']   = 0;
		$scores['r3']['win_in'] = 0;
		$scores['r4']           = array();
		$scores['r4']['team']   = 0;
		$scores['r4']['win_in'] = 0;
		$total_score            = 0;

		foreach ( $matchups as $round => $matchup ) {
			foreach ( $matchup as $m ) {
				$correct_array                   = array();
				$correct_array['team_correct']   = false;
				$correct_array['win_in_correct'] = false;
				if ( $playoff_meta[ 'matchup_' . $m ]['winner'] > 1 && $playoff_meta[ 'matchup_' . $m ]['winner'] === $pick_meta[ 'pick_' . $m ]['team'] ) {
					$scores[ $round ]['team']++;
					$correct_array['team_correct'] = true;

					if ( $playoff_meta[ 'matchup_' . $m ]['winner_in'] === $pick_meta[ 'pick_' . $m ]['win_in'] ) {
						$scores[ $round ]['win_in']++;
						$correct_array['win_in_correct'] = true;
					}
				}
				update_field( 'pick_' . $m, $correct_array, $pick_id );
			}
		}

		// Calculate scores.
		$r1_score = ( $scores['r1']['team'] * 1 ) + ( $scores['r1']['win_in'] * 2 );
		$r2_score = ( $scores['r2']['team'] * 3 ) + ( $scores['r2']['win_in'] * 2 );
		$r3_score = ( $scores['r3']['team'] * 5 ) + ( $scores['r3']['win_in'] * 2 );
		$r4_score = ( $scores['r4']['team'] * 7 ) + ( $scores['r4']['win_in'] * 2 );

		$total_score = $r1_score + $r2_score + $r3_score + $r4_score;

		// Update scores.
		update_field( 'r1_correct_team', $scores['r1']['team'], $pick_id );
		update_field( 'r1_correct_games', $scores['r1']['win_in'], $pick_id );
		update_field( 'r1_score', $r1_score, $pick_id );
		update_field( 'r2_correct_team', $scores['r2']['team'], $pick_id );
		update_field( 'r2_correct_games', $scores['r2']['win_in'], $pick_id );
		update_field( 'r2_score', $r2_score, $pick_id );
		update_field( 'r3_correct_team', $scores['r3']['team'], $pick_id );
		update_field( 'r3_correct_games', $scores['r3']['win_in'], $pick_id );
		update_field( 'r3_score', $r3_score, $pick_id );
		update_field( 'r4_correct_team', $scores['r4']['team'], $pick_id );
		update_field( 'r4_correct_games', $scores['r4']['win_in'], $pick_id );
		update_field( 'r4_score', $r4_score, $pick_id );
		update_field( 'total_score', $total_score, $pick_id );
	}
}
