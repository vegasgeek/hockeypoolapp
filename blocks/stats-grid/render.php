<?php
/**
 * Render the stats grid block.
 *
 * @package hockeypool
 */

$class_name = 'stats-grid';

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

// Grab some default variables.
$cur_year = vg_get_cur_year( $post_id );

// Get all player stats for current year.
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
	'orderby'        => 'meta_value_num',
	'meta_key'       => 'total_score', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	'order'          => 'DESC',
);
$picks = get_posts( $args );

ray()->clearAll(); // #jrh

?>
<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" style="">
	<h1><?php echo esc_attr( $cur_year ); ?> Stats</h1>

	<div class="stats">
		<table>
			<tr>
				<th class="c1">Player</th>
				<th class="c2">Round 1</th>
				<th class="c2">Round 2</th>
				<th class="c2">Round 3</th>
				<th class="c2">Stanley Cup</th>
				<th class="c2">Total</th>
			</tr>
			<?php foreach ( $picks as $pick_id ) : ?>
				<?php
				$pick_meta = get_fields( $pick_id );
				?>
				<tr>
					<td><a href="<?php the_permalink( $pick_id ) ?>"><?php echo esc_html( $pick_meta['player']['display_name'] ); ?></a></td>
					<td><?php echo esc_html( $pick_meta['r1_score'] ); ?></td>
					<td><?php echo esc_html( $pick_meta['r2_score'] ); ?></td>
					<td><?php echo esc_html( $pick_meta['r3_score'] ); ?></td>
					<td><?php echo esc_html( $pick_meta['r4_score'] ); ?></td>
					<td><?php echo esc_html( $pick_meta['total_score'] ); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>

	</div>
</div>
