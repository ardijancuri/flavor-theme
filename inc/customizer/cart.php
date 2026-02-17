<?php
/**
 * Customizer — Cart Settings
 *
 * @package flavor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register cart-related Customizer settings.
 */
function flavor_customizer_cart( $wp_customize ) {

	// Section
	$wp_customize->add_section( 'flavor_cart', array(
		'title'    => esc_html__( 'Cart Settings', 'flavor' ),
		'panel'    => 'flavor_panel',
		'priority' => 40,
	) );

	// Cross-sells on/off
	$wp_customize->add_setting( 'flavor_cart_cross_sells', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'flavor_cart_cross_sells', array(
		'label'   => esc_html__( 'Show Cross-sells on Cart', 'flavor' ),
		'section' => 'flavor_cart',
		'type'    => 'checkbox',
	) );

	// Warranty add-on on/off
	$wp_customize->add_setting( 'flavor_cart_warranty', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'flavor_cart_warranty', array(
		'label'       => esc_html__( 'Enable Warranty Add-on', 'flavor' ),
		'description' => esc_html__( 'Show warranty option per cart item when _warranty_price meta is set.', 'flavor' ),
		'section'     => 'flavor_cart',
		'type'        => 'checkbox',
	) );
}
add_action( 'customize_register', 'flavor_customizer_cart' );
