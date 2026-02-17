<?php
/**
 * Customizer — Product page settings
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'customize_register', 'flavor_customizer_product' );

function flavor_customizer_product( $wp_customize ) {

	$wp_customize->add_section( 'flavor_product', array(
		'title'    => __( 'Product Page', 'flavor' ),
		'priority' => 131,
	) );

	// Installment on/off
	$wp_customize->add_setting( 'flavor_installment_enabled', array(
		'default'           => true,
		'sanitize_callback' => 'flavor_sanitize_checkbox',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'flavor_installment_enabled', array(
		'label'   => __( 'Show installment calculator', 'flavor' ),
		'section' => 'flavor_product',
		'type'    => 'checkbox',
	) );

	// Warranty on/off
	$wp_customize->add_setting( 'flavor_warranty_enabled', array(
		'default'           => true,
		'sanitize_callback' => 'flavor_sanitize_checkbox',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'flavor_warranty_enabled', array(
		'label'   => __( 'Show warranty upsell', 'flavor' ),
		'section' => 'flavor_product',
		'type'    => 'checkbox',
	) );

	// Shipping zones (JSON repeater stored as theme_mod)
	$wp_customize->add_setting( 'flavor_shipping_zones', array(
		'default'           => wp_json_encode( array(
			array( 'name' => 'Metro', 'days' => 2 ),
			array( 'name' => 'Regional', 'days' => 4 ),
			array( 'name' => 'Remote', 'days' => 7 ),
		) ),
		'sanitize_callback' => 'flavor_sanitize_shipping_zones',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'flavor_shipping_zones', array(
		'label'       => __( 'Shipping zones (JSON)', 'flavor' ),
		'description' => __( 'JSON array: [{"name":"Zone","days":3}]', 'flavor' ),
		'section'     => 'flavor_product',
		'type'        => 'textarea',
	) );

	// Tab: show description
	$wp_customize->add_setting( 'flavor_tab_description', array(
		'default'           => true,
		'sanitize_callback' => 'flavor_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'flavor_tab_description', array(
		'label'   => __( 'Show Description tab', 'flavor' ),
		'section' => 'flavor_product',
		'type'    => 'checkbox',
	) );

	// Tab: show specifications
	$wp_customize->add_setting( 'flavor_tab_specifications', array(
		'default'           => true,
		'sanitize_callback' => 'flavor_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'flavor_tab_specifications', array(
		'label'   => __( 'Show Specifications tab', 'flavor' ),
		'section' => 'flavor_product',
		'type'    => 'checkbox',
	) );

	// Tab: show reviews
	$wp_customize->add_setting( 'flavor_tab_reviews', array(
		'default'           => true,
		'sanitize_callback' => 'flavor_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'flavor_tab_reviews', array(
		'label'   => __( 'Show Reviews tab', 'flavor' ),
		'section' => 'flavor_product',
		'type'    => 'checkbox',
	) );
}

/**
 * Sanitize checkbox
 */
function flavor_sanitize_checkbox( $input ) {
	return (bool) $input;
}

/**
 * Sanitize shipping zones JSON
 */
function flavor_sanitize_shipping_zones( $input ) {
	$decoded = json_decode( $input, true );
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}
	$clean = array();
	foreach ( $decoded as $zone ) {
		if ( isset( $zone['name'], $zone['days'] ) ) {
			$clean[] = array(
				'name' => sanitize_text_field( $zone['name'] ),
				'days' => absint( $zone['days'] ),
			);
		}
	}
	return wp_json_encode( $clean );
}
