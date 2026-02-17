<?php
/**
 * Customizer — Shop settings
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', 'flavor_customizer_shop' );

function flavor_customizer_shop( $wp_customize ) {

	$wp_customize->add_section( 'flavor_shop', array(
		'title'    => __( 'Shop Settings', 'flavor' ),
		'priority' => 130,
	) );

	// Products per page
	$wp_customize->add_setting( 'flavor_products_per_page', array(
		'default'           => 12,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'flavor_products_per_page', array(
		'label'       => __( 'Products per page', 'flavor' ),
		'section'     => 'flavor_shop',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 4, 'max' => 48, 'step' => 1 ),
	) );

	// Grid columns
	$wp_customize->add_setting( 'flavor_grid_columns', array(
		'default'           => 4,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'flavor_grid_columns', array(
		'label'   => __( 'Grid columns (desktop)', 'flavor' ),
		'section' => 'flavor_shop',
		'type'    => 'select',
		'choices' => array(
			3 => '3',
			4 => '4',
			5 => '5',
		),
	) );
}
