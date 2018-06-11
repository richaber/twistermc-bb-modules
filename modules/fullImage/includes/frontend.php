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

$image         = get_post( $settings->photo_field );
$image_caption = $image->post_excerpt;

?>

<div class="tm_bb_fullImage">
    <?php if ($settings->linkImage == 'true' && $settings->linkurl != '') { echo '<a href="' . $settings->linkurl .'" target="' . $settings->linktarget .'">'; } ?>
    <?php echo wp_get_attachment_image( $settings->photo_field, $size = 'full', $icon = false, $attr = '' ); ?>
    <?php if ($settings->linkImage == 'true') { echo '</a>'; } ?>
</div>

<?php if ($settings->showCaption == 'true') { ?>
    <div class="tm_bb_fullImage_caption"><?php echo $image_caption; ?></div>
<?php } ?>
