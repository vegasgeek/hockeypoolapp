<?php
/**
 * Render the stats grid block.
 *
 * @package hockeypool
 */

$class_name = 'picks-team';

$block_id = $class_name . '-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$block_id = $block['anchor'];
}

if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

$picks = get_fields( $post_id );

// Grab some default variables.
$cur_year = vg_get_cur_year( $post_id );
$player   = $picks['player']['nickname'];

$args = array(
	'post_type' => 'playoffs',
	'post_status' => 'publish',
	'post_per_page' => 1,
	'fields' => 'ids',
	'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'playoff_year',
			'field'    => 'slug',
			'terms'    => $cur_year,
		),
	),
);
$playoff_ids = get_posts( $args );
$playoff_id  = $playoff_ids[0];

function show_team( $playoff_id, $matchup, $team_num ) {
	global $post;
	$playoff_meta = get_fields( $playoff_id );
	$picks        = get_fields( $post->ID );

	if ( ! isset( $picks[ 'pick_' . $matchup ]['team'] ) ) {
		return;
	}

	if ( strlen( $picks[ 'pick_' . $matchup ]['team'] ) < 1 ) {
		return;
	}
	$team_id   = $playoff_meta[ 'matchup_' . $matchup ][ 'team_' . $team_num ]->ID;
	$team_name = $playoff_meta[ 'matchup_' . $matchup ][ 'team_' . $team_num ]->post_title;
	$pick_id   = $picks[ 'pick_' . $matchup ]['team'];
	$picked_in = $picks[ 'pick_' . $matchup ]['win_in'];
	$winner    = $playoff_meta[ 'matchup_' . $matchup ]['winner'] ?? null;
	$winner_in = $playoff_meta[ 'matchup_' . $matchup ]['winner_in'] ?? null;
	$thumbnail = get_the_post_thumbnail_url( $team_id, 'thumbnail' );
	$picked_count_class = 'default';

	if ( intval( $pick_id ) === intval( $winner ) && intval( $pick_id ) === intval( $team_id ) ) {
		$correct_guess = 'correct';
		if ( intval( $winner_in ) === intval( $picked_in ) ) {
			$picked_count_class = 'correct';
		} else {
			$picked_count_class = 'incorrect';
		}
	} else {
		$correct_guess = 'incorrect';
	}

	if ( intval( $team_id ) === intval( $pick_id ) ) {
		$picked_games = '<div class="picked-games ' . $picked_count_class . '">' . $picked_in . '</div>';
		$picked_class = 'picked';
	} else {
		$picked_games = '';
		$picked_class = 'notpicked';
	}

	$output = '<div class="team ' . $correct_guess . '"><img src="' . $thumbnail . '" alt="' . $team_name . '" class=" '. $picked_class . '" />' . $picked_games . '</div>';

	return $output;
}

function show_matchup_result( $playoff_id, $matchup ) {
	global $post;
	$playoff_meta = get_fields( $playoff_id );

	if ( isset( $playoff_meta[ 'matchup_' . $matchup ]['winner'] ) && strlen( $playoff_meta[ 'matchup_' . $matchup ]['winner'] ) > 0 ) {
		$winner_id  = $playoff_meta[ 'matchup_' . $matchup ]['winner'];
		$loser_wins = $playoff_meta[ 'matchup_' . $matchup ]['winner_in'] - 4;
		$winner = get_the_title($winner_id) . '<br />4 - ' . $loser_wins;
	} else {
		$winner = 'TBD';
	}

	return $winner;
}

function show_matchup($playoff_id, $matchup)
{
	$team_1 = show_team($playoff_id, $matchup, 1);
	$team_2 = show_team($playoff_id, $matchup, 2);
	$winner = show_matchup_result($playoff_id, $matchup);
	$html  = '<div class="nested-grid">';
	$html .= '<div class="row">';
	$html .= '<div class="nested-col6">' . $team_1 . '</div>';
	$html .= '<div class="nested-col6">' . $team_2 . '</div>';
	$html .= '</div>';
	$html .= '<div class="row">';
	$html .= '<div class="nested-col12">' . $winner . '</div>';
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}

?>
<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" style="">
	<h1><?php echo esc_attr( $player ); ?>'s picks for <?php echo esc_attr( $cur_year ); ?></h1>

	<hr />

	<div class="round1">
		<h2>Round 1</h2>
		<div class="grid">
		<?php
		$matchups = array( 'w_1_1', 'w_1_2', 'w_1_3', 'w_1_4', 'e_1_1', 'e_1_2', 'e_1_3', 'e_1_4' );

		foreach ( $matchups as $matchup ) {
			echo show_matchup( $playoff_id, $matchup );
		}
		?>
		</div>
	</div>

	<hr class="icon">

	<div class="round2">
		<h2>Round 2</h2>
		<div class="grid">
		<?php
		$matchups = array( 'w_2_1', 'w_2_2', 'e_2_1', 'e_2_2' );

		foreach ( $matchups as $matchup ) {
			echo show_matchup( $playoff_id, $matchup );
		}
		?>
		</div>
	</div>

	<hr class="icon">

	<div class="round3">
		<h2>Round 3</h2>
		<div class="grid">
		<?php
		$matchups = array( 'w_3_1', 'e_3_1' );

		foreach ( $matchups as $matchup ) {
			echo show_matchup( $playoff_id, $matchup );
		}
		?>
		</div>
	</div>

	<hr class="icon">

	<div class="round4">
		<h2>Stanley Cup</h2>
		<div class="grid">
		<?php
		$matchups = array( 'stanley_cup' );

		foreach ( $matchups as $matchup ) {
			echo show_matchup( $playoff_id, $matchup );
		}
		?>
		</div>
	</div>

</div>
