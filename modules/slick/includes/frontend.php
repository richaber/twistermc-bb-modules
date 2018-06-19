<?php
/**
 * BBSlickSlider "frontend HTML" file.
 *
 * Used by Beaver Builder to generate the markup output.
 *
 * @see     \BBSlickSlider
 * @see     \FLBuilderModule
 *
 * @link    https://kb.wpbeaverbuilder.com/article/600-cmdg-05-module-html
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
 * If we don't have any slides, we won't output any of the inner content.
 * However, we will output the wrapping div, just so we have some kind of markup in the Page Builder for targeting.
 */

?>

<div class="slickModule_bb">

	<?php if ( $module->has_slides() ) : ?>

		<div class="slickWrapper_bb">

			<?php foreach ( $module->get_slides() as $index => $slide ) : ?>

				<?php $module->the_slide( $slide, $index ); ?>

			<?php endforeach; ?>

		</div>

		<button class="fa fa-pause-circle js-slickModule_bb_Pause slickModule_bb_Pause" aria-hidden="true">
			<span class="screen-reader-text">
				<?php esc_html_e( 'Pause', 'tmcbbm' ); ?>
			</span>
		</button>

	<?php endif; ?>

</div>
