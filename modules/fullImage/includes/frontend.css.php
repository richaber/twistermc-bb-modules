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

?>

<?php if ($settings->forceImageSize == 'true') { ?>
.fl-node-<?php echo $id; ?> .tm_bb_fullImage img {
    width: 100%;
    height: auto;
}
<?php } else { ?>
.fl-node-<?php echo $id; ?> .tm_bb_fullImage {
    text-align: <?php echo $settings->imageAlign; ?>;
}
<?php } ?>

<?php if ($settings->showCaption == 'true') {
    if ($settings->captionBackgroundColor == '') {
        $backgroundColor = 'transparent';
    } else {
        $backgroundColor = '#' . $settings->captionBackgroundColor;
    }

    if ($settings->captionPadding == '') {
        $captionPadding = '0';
    } else {
        $captionPadding = $settings->captionPadding;
    }

    ?>
.fl-node-<?php echo $id; ?> .tm_bb_fullImage_caption {
    padding: <?php echo $captionPadding ?>px;
    background: <?php echo $backgroundColor; ?>;
    text-align: <?php echo $settings->captionAlign; ?>;
    color: #<?php echo $settings->captionColor; ?>;
    font-size: <?php echo $settings->captionSize; ?>px;
}
<?php } ?>