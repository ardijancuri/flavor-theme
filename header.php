<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        [x-cloak]{display:none!important}
        body{opacity:1;transition:opacity .15s ease}
        body.is-navigating{opacity:0}
    </style>
    <script>
        // Smooth page transitions — fade out on navigate, fade in on load
        document.addEventListener('click', function(e) {
            var link = e.target.closest('a[href]');
            if (!link) return;
            var href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || link.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
            if (new URL(href, location.origin).origin !== location.origin) return;
            e.preventDefault();
            document.body.classList.add('is-navigating');
            setTimeout(function() { window.location.href = href; }, 120);
        });
        // Ensure body is visible on back/forward cache restore
        window.addEventListener('pageshow', function(e) {
            document.body.classList.remove('is-navigating');
        });
    </script>
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
