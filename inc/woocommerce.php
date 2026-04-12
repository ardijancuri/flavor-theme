<?php
/**
 * WooCommerce integration helpers.
 *
 * @package Flavor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the custom mega menu image attachment ID for a product category.
 *
 * @param int $term_id Product category term ID.
 * @return int
 */
function flavor_get_product_cat_mega_menu_image_id( $term_id ) {
	return absint( get_term_meta( $term_id, '_flavor_mega_menu_image_id', true ) );
}

/**
 * Render the admin preview markup for a mega menu category image.
 *
 * @param int $attachment_id Attachment ID.
 * @return string
 */
function flavor_get_product_cat_mega_menu_preview_html( $attachment_id ) {
	$attachment_id = absint( $attachment_id );

	if ( ! $attachment_id ) {
		return '';
	}

	$image_url = wp_get_attachment_image_url( $attachment_id, 'medium' );

	if ( ! $image_url ) {
		return '';
	}

	return sprintf(
		'<img src="%1$s" alt="" style="display:block;max-width:220px;width:100%%;height:auto;border-radius:8px;">',
		esc_url( $image_url )
	);
}

/**
 * Product category add form field for mega menu image.
 */
function flavor_product_cat_add_mega_menu_image_field() {
	?>
	<div class="form-field flavor-mega-menu-image-field">
		<label for="flavor-mega-menu-image-id"><?php esc_html_e( 'Mega Menu Image', 'flavor' ); ?></label>
		<input type="hidden" id="flavor-mega-menu-image-id" name="flavor_mega_menu_image_id" value="">
		<div class="flavor-mega-menu-image-preview" style="margin:12px 0;"></div>
		<div style="display:flex;gap:8px;align-items:center;">
			<button type="button" class="button flavor-mega-menu-image-upload"><?php esc_html_e( 'Select image', 'flavor' ); ?></button>
			<button type="button" class="button flavor-mega-menu-image-remove" style="display:none;"><?php esc_html_e( 'Remove image', 'flavor' ); ?></button>
		</div>
		<p class="description"><?php esc_html_e( 'Displayed on the right side of the desktop categories mega menu.', 'flavor' ); ?></p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'flavor_product_cat_add_mega_menu_image_field' );

/**
 * Product category edit form field for mega menu image.
 *
 * @param WP_Term $term Product category term.
 */
function flavor_product_cat_edit_mega_menu_image_field( $term ) {
	$image_id = flavor_get_product_cat_mega_menu_image_id( $term->term_id );
	?>
	<tr class="form-field flavor-mega-menu-image-field">
		<th scope="row">
			<label for="flavor-mega-menu-image-id"><?php esc_html_e( 'Mega Menu Image', 'flavor' ); ?></label>
		</th>
		<td>
			<input type="hidden" id="flavor-mega-menu-image-id" name="flavor_mega_menu_image_id" value="<?php echo esc_attr( $image_id ); ?>">
			<div class="flavor-mega-menu-image-preview" style="margin:0 0 12px;">
				<?php echo wp_kses_post( flavor_get_product_cat_mega_menu_preview_html( $image_id ) ); ?>
			</div>
			<div style="display:flex;gap:8px;align-items:center;">
				<button type="button" class="button flavor-mega-menu-image-upload"><?php esc_html_e( 'Select image', 'flavor' ); ?></button>
				<button type="button" class="button flavor-mega-menu-image-remove" <?php echo $image_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove image', 'flavor' ); ?></button>
			</div>
			<p class="description"><?php esc_html_e( 'Displayed on the right side of the desktop categories mega menu.', 'flavor' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'flavor_product_cat_edit_mega_menu_image_field' );

/**
 * Save product category mega menu image.
 *
 * @param int $term_id Product category term ID.
 */
function flavor_save_product_cat_mega_menu_image( $term_id ) {
	if ( ! isset( $_POST['flavor_mega_menu_image_id'] ) ) {
		return;
	}

	$image_id = absint( wp_unslash( $_POST['flavor_mega_menu_image_id'] ) );

	if ( $image_id ) {
		update_term_meta( $term_id, '_flavor_mega_menu_image_id', $image_id );
	} else {
		delete_term_meta( $term_id, '_flavor_mega_menu_image_id' );
	}
}
add_action( 'created_product_cat', 'flavor_save_product_cat_mega_menu_image' );
add_action( 'edited_product_cat', 'flavor_save_product_cat_mega_menu_image' );

/**
 * Enqueue media uploader support for product category mega menu images.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function flavor_product_cat_mega_menu_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'product_cat' !== $screen->taxonomy ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'jquery' );

	$script = "
	jQuery(function($) {
		function updateField(field, attachment) {
			var input = field.find('input[name=\"flavor_mega_menu_image_id\"]');
			var preview = field.find('.flavor-mega-menu-image-preview');
			var remove = field.find('.flavor-mega-menu-image-remove');

			if (attachment && attachment.id) {
				input.val(attachment.id);
				preview.html('<img src=\"' + attachment.url + '\" alt=\"\" style=\"display:block;max-width:220px;width:100%;height:auto;border-radius:8px;\">');
				remove.show();
			} else {
				input.val('');
				preview.empty();
				remove.hide();
			}
		}

		$(document).on('click', '.flavor-mega-menu-image-upload', function(event) {
			event.preventDefault();

			var button = $(this);
			var field = button.closest('.flavor-mega-menu-image-field');
			var frame = wp.media({
				title: 'Select mega menu image',
				button: { text: 'Use image' },
				multiple: false
			});

			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				updateField(field, attachment);
			});

			frame.open();
		});

		$(document).on('click', '.flavor-mega-menu-image-remove', function(event) {
			event.preventDefault();
			updateField($(this).closest('.flavor-mega-menu-image-field'), null);
		});
	});
	";

	wp_add_inline_script( 'jquery', $script );
}
add_action( 'admin_enqueue_scripts', 'flavor_product_cat_mega_menu_admin_assets' );
