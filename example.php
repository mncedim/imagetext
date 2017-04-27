<?php
/**
 * Created by PhpStorm.
 * User: mncera
 * Date: 17/04/27
 * Time: 1:04 PM
 */

require __DIR__.'/src/ImageText.php';

use Mncedim\ImageText;

ImageText::setFontFile(__DIR__.'/example/arial.ttf');
ImageText::setFontColour('#ffffff');
ImageText::setFontSize(18);

ImageText::align(ImageText::ALIGN_CENTER);
ImageText::vAlign(ImageText::V_ALIGN_MIDDLE);

ImageText::setBackground(__DIR__.'/example/map.png');

ImageText::create(
    ImageText::TYPE_PNG, 'The stage is set!'
);