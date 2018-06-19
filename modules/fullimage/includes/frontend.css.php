<?php
/**
 * BBFullImage "frontend CSS" file.
 *
 * Used by Beaver Builder to generate frontend styles that will be applied to individual module instances.
 *
 * @see     \BBFullImage
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

if ( empty( $settings->captionBackgroundColor ) ) {
	$backgroundColor = 'transparent';
} else {
	$backgroundColor = '#' . $settings->captionBackgroundColor;
}

if ( empty( $settings->captionPadding ) ) {
	$captionPadding = '0';
} else {
	$captionPadding = $settings->captionPadding;
}

?>

<?php if ( 'true' === $settings->forceImageSize ) : ?>

.fl-node-<?php echo esc_attr( $id ); ?> .tm_bb_fullImage img {
	width: 100%;
	height: auto;
}

<?php else : ?>

.fl-node-<?php echo esc_attr( $id ); ?> .tm_bb_fullImage {
	text-align: <?php echo esc_attr( $settings->imageAlign ); ?>;
}

<?php endif; ?>

<?php if ( 'true' === $settings->showCaption ) : ?>

.fl-node-<?php echo esc_attr( $id ); ?> .tm_bb_fullImage_caption {
	padding: <?php echo esc_attr( $captionPadding ); ?>px;
	background: <?php echo esc_attr( $backgroundColor ); ?>;
	text-align: <?php echo esc_attr( $settings->captionAlign ); ?>;
	color: #<?php echo esc_attr( $settings->captionColor ); ?>;
	font-size: <?php echo esc_attr( $settings->captionSize ); ?>px;
}

<?php endif; ?>
