<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> x-data="{ mobileMenuOpen: false, miniCartOpen: false, searchOpen: false }">
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header/top-bar' ); ?>
<?php get_template_part( 'template-parts/header/main-header' ); ?>
<?php get_template_part( 'template-parts/header/quick-links' ); ?>
<?php get_template_part( 'template-parts/header/mega-menu' ); ?>
<?php get_template_part( 'template-parts/header/mobile-menu' ); ?>
<?php get_template_part( 'template-parts/header/mini-cart' ); ?>
