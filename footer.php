<?php
/**
 * Footer template
 *
 * @package Flavor
 */

defined( 'ABSPATH' ) || exit;
?>

<footer class="bg-black-dark text-white mt-auto">
    <div class="container-site py-10">
        <div class="grid grid-cols-1 tablet-sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php get_template_part( 'template-parts/footer/column', 'brand' ); ?>
            <?php get_template_part( 'template-parts/footer/column', 'account' ); ?>
            <?php get_template_part( 'template-parts/footer/column', 'faq' ); ?>
            <?php get_template_part( 'template-parts/footer/column', 'contact' ); ?>
        </div>
    </div>

    <?php get_template_part( 'template-parts/footer/ecosystem' ); ?>

    <div class="text-center text-xs text-gray-500 py-4 border-t border-gray-700">
        &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'flavor' ); ?>
    </div>
</footer>

<?php get_template_part( 'template-parts/global/bottom-nav' ); ?>
<?php get_template_part( 'template-parts/global/cookie-consent' ); ?>
<?php get_template_part( 'template-parts/global/toast-notifications' ); ?>
<?php get_template_part( 'template-parts/global/scroll-to-top' ); ?>

<?php wp_footer(); ?>
</body>
</html>
