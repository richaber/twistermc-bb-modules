<?php
/**
 * BBSlickSlider "frontend CSS" file.
 *
 * Used by Beaver Builder to generate frontend styles that will be applied to individual module instances.
 *
 * @see     \BBSlickSlider
 *
 * @var \BBSlickSlider $module   An instance of the module class.
 * @var string         $id       The module's node ID ( i.e. $module->node ).
 * @var stdClass       $settings The module's settings ( i.e. $module->settings ).
 *
 * @package TwisterMcBBModules
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * If we don't have any slides,
 * echo an empty string (BB expects some kind of output for building caches and such),
 * and return.
 */
if ( ! $module->has_slides() ) {

	echo '';

	return;
}

// @codingStandardsIgnoreStart

?>

.fl-node-<?php echo esc_attr( $id ); ?> .slick-arrow,
.fl-node-<?php echo esc_attr( $id ); ?> .slickModule_bb_Pause {
	<?php if ( ! empty( $settings->arrowBackgroundColor ) ) : ?>
	background: <?php echo esc_attr( '#' . $settings->arrowBackgroundColor ); ?>;
	<?php else : ?>
	background: transparent;
	<?php endif; ?>
	<?php if ( ! empty( $settings->arrowColor ) ) : ?>
	color: <?php echo esc_attr( '#' . $settings->arrowColor ); ?>;
	<?php endif; ?>
}

.fl-node-<?php echo esc_attr( $id ); ?> .slick-arrow:before,
.fl-node-<?php echo esc_attr( $id ); ?> .slickModule_bb_Pause:before {
	<?php if ( ! empty( $settings->arrowSize ) ) : ?>
	font-size: <?php echo esc_attr( $settings->arrowSize ); ?>px;
	<?php endif; ?>
}

.fl-node-<?php echo esc_attr( $id ); ?> .slick-arrow:hover,
.fl-node-<?php echo esc_attr( $id ); ?> .slickModule_bb_Pause:hover {
	<?php if ( ! empty( $settings->arrowHoverBackgroundColor ) ) : ?>
	background: <?php echo esc_attr( '#' . $settings->arrowHoverBackgroundColor ); ?>;
	<?php else : ?>
	background: transparent;
	<?php endif; ?>
	<?php if ( ! empty( $settings->arrowHoverColor ) ) : ?>
	color: <?php echo esc_attr( '#' . $settings->arrowHoverColor ); ?>;
	<?php endif; ?>
}

.fl-node-<?php echo esc_attr( $id ); ?> .slick-dots button {
	<?php if ( ! empty( $settings->dotBackgroundColor ) ) : ?>
	background: <?php echo esc_attr( '#' . $settings->dotBackgroundColor ); ?>;
	<?php else : ?>
	background: transparent;
	<?php endif; ?>
	<?php if ( ! empty( $settings->dotColor ) ) : ?>
	color: <?php echo esc_attr( '#' . $settings->dotColor ); ?>;
	<?php endif; ?>
	<?php if ( ! empty( $settings->dotSize ) ) : ?>
	font-size: <?php echo esc_attr( $settings->dotSize ); ?>px;
	<?php endif; ?>
}

.fl-node-<?php echo esc_attr( $id ); ?> .slick-dots button:hover {
	<?php if ( ! empty( $settings->dotHoverBackgroundColor ) ) : ?>
	background: <?php echo esc_attr( '#' . $settings->dotHoverBackgroundColor ); ?>;
	<?php else : ?>
	background: transparent;
	<?php endif; ?>
	<?php if ( ! empty( $settings->dotHoverColor ) ) : ?>
	color: <?php echo esc_attr( '#' . $settings->dotHoverColor ); ?>;
	<?php endif; ?>
}

.fl-node-<?php echo esc_attr( $id ); ?> .slick-active button {
	<?php if ( ! empty( $settings->dotActiveBackgroundColor ) ) : ?>
	background: <?php echo esc_attr( '#' . $settings->dotActiveBackgroundColor ); ?>;
	<?php else : ?>
	background: transparent;
	<?php endif; ?>
	<?php if ( ! empty( $settings->dotActiveColor ) ) : ?>
	color: <?php echo esc_attr( '#' . $settings->dotActiveColor ); ?>;
	<?php endif; ?>
}


<?php if ( 'true' === $settings->forceImageSize && 'false' === $settings->oneSlide ) : ?>
	/* ---------------------------------------------------------------------
	Force Image Size
	------------------------------------------------------------------------ */
	.fl-node-<?php echo esc_attr( $id ); ?> .slick-slide img {
		width: 100%;
		height: auto;
	}
<?php endif; ?>


<?php if ( 'true' === $settings->showCaptions ) : ?>
	/* ---------------------------------------------------------------------
	Photo Captions
	------------------------------------------------------------------------ */
	.fl-node-<?php echo esc_attr( $id ); ?> .slickPhotoCaption {
		background-color: #000;
		padding: 10px;
		text-align: center;
		color: #fff;
	}
<?php endif; ?>


<?php if ( 'false' === $settings->adaptiveHeight ) : ?>
	/* ---------------------------------------------------------------------
	Fixed Height Size
	------------------------------------------------------------------------ */
	.fl-node-<?php echo esc_attr( $id ); ?> .slick-slide {
		<?php if ( ! empty( $settings->fixedHeightSize ) ) : ?>
		height: <?php echo esc_attr( $settings->fixedHeightSize ); ?>px;
		<?php else : ?>
		height: 500px;
		<?php endif; ?>
	}

	.fl-node-<?php echo esc_attr( $id ); ?> .slick-slide img {
		max-height: 100%;
	}
<?php endif; ?>
