<?php
/**
 * Created by PhpStorm.
 * User: mncera
 * Date: 17/04/27
 * Time: 1:04 PM
 */

require __DIR__.'/src/ImageText.php';

use Mncedim\Image\ImageText;

ImageText::setFontFile(__DIR__.'/example/arial.ttf');
ImageText::setFontColour('#ffffff');
ImageText::setFontSize(18);

ImageText::align(ImageText::ALIGN_CENTER);
ImageText::vAlign(ImageText::V_ALIGN_BOTTOM);

ImageText::setBackground(__DIR__.'/example/map.png');

ImageText::create(
    ImageText::TYPE_PNG, 'The stage is set!', null
);

$image = ImageText::getRaw();

header( "Content-type: image/png" );
header("Cache-Control: no-cache, max-age=0");

imagepng($image, null, 8);
imagedestroy($image);