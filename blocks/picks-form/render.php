<?php
/**
 * Render the form to make picks.
 *
 * @package hockeypool
 */

$class_name = 'picks-form';

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
?>
<div id="<?php echo esc_attr( $block_id ); ?>" class="<?php echo esc_attr( $class_name ); ?>" style="">
	<h1>Make your picks</h1>
	<?php
	$cur_year = wp_date( 'Y' );
	$user_id  = get_current_user_id();

	$args = array(
		'post_type'     => 'picks',
		'post_status'   => 'publish',
		'post_per_page' => 1,
		'author'        => $user_id,
		'tax_query'     => array(
			array(
				'taxonomy' => 'playoff_year',
				'field'    => 'slug',
				'terms'    => $cur_year,
			),
		),
	);

	$the_query = new WP_Query( $args );

	// The Loop.
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			$pick_post_id = $the_query->post->ID;

			acf_form(
				array(
					'field_groups' => array( 152 ),
					'post_id'      => $pick_post_id,
				)
			);
		}
	}

	// Reset Post Data.
	wp_reset_postdata();
	?>
</div>
