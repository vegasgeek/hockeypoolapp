<?php
/**
 * ACF Prefills
 *
 * @package HockeyPool
 */

/**
 * Prefill picks & matchup dropdowns.
 *
 * @param array $field Field data.
 *
 * @return array Modified field data.
 */
function vg_prefill_matchup( $field ) {
	switch ( $field['key'] ) {
		// Round 1.
		case 'field_64600b30a209e':
		case 'field_646051f7d8d99':
			$field['choices'] = vg_prefill_query( 'w_1_1' );
			break;
		case 'field_64600e274eb50':
		case 'field_64605356f5e45':
			$field['choices'] = vg_prefill_query( 'w_1_2' );
			break;
		case 'field_64600f1655806':
		case 'field_64605359f5e4d':
			$field['choices'] = vg_prefill_query( 'w_1_3' );
			break;
		case 'field_64600f8655808':
		case 'field_6460535cf5e55':
			$field['choices'] = vg_prefill_query( 'w_1_4' );
			break;
		case 'field_64601059b5a35':
		case 'field_6460539af5e5f':
			$field['choices'] = vg_prefill_query( 'e_1_1' );
			break;
		case 'field_6460108bb5a37':
		case 'field_646053b7f5e67':
			$field['choices'] = vg_prefill_query( 'e_1_2' );
			break;
		case 'field_646010adb5a39':
		case 'field_646053bef5e6f':
			$field['choices'] = vg_prefill_query( 'e_1_3' );
			break;
		case 'field_646010deb5a3b':
		case 'field_646053c2f5e77':
			$field['choices'] = vg_prefill_query( 'e_1_4' );
			break;

		// Round 2.
		case 'field_64601134dc2d3':
		case 'field_64605442e18af':
			$field['choices'] = vg_prefill_query( 'w_2_1' );
			break;
		case 'field_64601146dc2e1':
		case 'field_6460547ce18b9':
			$field['choices'] = vg_prefill_query( 'w_2_2' );
			break;
		case 'field_646011c3dc2f1':
		case 'field_64605481e18c1':
			$field['choices'] = vg_prefill_query( 'e_2_1' );
			break;
		case 'field_64601219dc301':
		case 'field_64605491e18c9':
			$field['choices'] = vg_prefill_query( 'e_2_2' );
			break;

		// Round 3.
		case 'field_646012fb66495':
		case 'field_64605771b2df8':
			$field['choices'] = vg_prefill_query( 'w_3_1' );
			break;
		case 'field_64601315664a3':
		case 'field_64605779b2e00':
			$field['choices'] = vg_prefill_query( 'e_3_1' );
			break;

		// Round 4.
		case 'field_64601345664b1':
		case 'field_646057ccb2e08':
			$field['choices'] = vg_prefill_query( 'stanley_cup' );
			break;

	}

	return $field;
}

$field_ids = array( '64600b30a209e', '64600e274eb50', '64600f1655806', '64600f8655808', '64601059b5a35', '6460108bb5a37', '646010adb5a39', '646010deb5a3b', '64601134dc2d3', '64601146dc2e1', '646011c3dc2f1', '64601219dc301', '646012fb66495', '64601315664a3', '64601345664b1', '646051f7d8d99', '64605356f5e45', '64605359f5e4d', '6460535cf5e55', '6460539af5e5f', '646053b7f5e67', '646053bef5e6f', '646053c2f5e77', '64605442e18af', '6460547ce18b9', '64605481e18c1', '64605491e18c9', '64605771b2df8', '64605779b2e00', '646057ccb2e08' );

foreach ( $field_ids as $field_id ) {
	add_filter( 'acf/load_field/key=field_' . $field_id, 'vg_prefill_matchup' );
}

/**
 * Prefill ACF fields with matchup data.
 *
 * @param string $matchup Matchup.
 *
 * @return array matchup data.
 */
function vg_prefill_query( $matchup ) {
	if ( ! $matchup ) {
		return array();
	}

	global $post;
	if ( ! isset( $post->ID ) ) {
		return array();
	}

	$cur_year = vg_get_cur_year( $post->ID );

	if ( 'playoffs' === $post->post_type ) {
		$playoff_id = $post->ID;
	} else {
		$args       = array(
			'post_type'      => 'playoffs',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'playoff_year',
					'field'    => 'slug',
					'terms'    => $cur_year,
				),
			),
		);
		$playoff    = get_posts( $args );
		$playoff_id = $playoff[0];
	}

	$teams  = array();
	$team_1 = get_post_meta( $playoff_id, 'matchup_' . $matchup . '_team_1', true );
	$team_2 = get_post_meta( $playoff_id, 'matchup_' . $matchup . '_team_2', true );

	if ( $team_1 && $team_2 ) {
		$teams = array(
			$team_1 => get_the_title( $team_1 ),
			$team_2 => get_the_title( $team_2 ),
		);
	}
	return $teams;
}
