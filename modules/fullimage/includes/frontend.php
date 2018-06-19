<?php
/**
 * BBFullImage "frontend HTML" file.
 *
 * Used by Beaver Builder to generate the markup output.
 *
 * @see     \BBFullImage
 * @see     \FLBuilderModule
 *
 * @link    https://kb.wpbeaverbuilder.com/article/600-cmdg-05-module-html
 *
 * @var \BBFullImage $module   An instance of the module class.
 * @var string       $id       The module's node ID ( i.e. $module->node ).
 * @var stdClass     $settings The module's settings ( i.e. $module->settings ).
 *
 * @package TwisterMcBBModules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $settings->photo_field ) ) {
	$image = get_post( $settings->photo_field );
}

if ( ! empty( $image ) ) {
	$image_caption = $image->post_excerpt;
}

?>

<div class="tm_bb_fullImage">

	<?php if ( ! empty( $settings->linkurl ) && 'true' === $settings->linkImage ) : ?>
		<a href="<?php echo esc_url( $settings->linkurl ); ?>" target="<?php echo esc_attr( $settings->linktarget ); ?>">
	<?php endif; ?>

		<?php if ( ! empty( $image->ID ) ) : ?>
			<?php echo wp_get_attachment_image( $image->ID, 'full' ); ?>
		<?php endif; ?>

	<?php if ( ! empty( $settings->linkurl ) && 'true' === $settings->linkImage ) : ?>
		</a>
	<?php endif; ?>

</div>

<?php if ( ! empty( $image_caption ) && 'true' === $settings->showCaption ) : ?>
	<div class="tm_bb_fullImage_caption">
		<?php echo wp_kses_post( $image_caption ); ?>
	</div>
<?php endif; ?>
