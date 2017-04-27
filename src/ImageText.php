<?php
/**
 * Created by PhpStorm.
 * User: mncera
 * Date: 17/04/27
 * Time: 10:23 AM
 */

namespace Mncedim\ImageText;

/**
 * Class ImageText
 * @package Mncedim
 */
class ImageText
{

    const ALIGN_LEFT        = 'L';
    const ALIGN_CENTER      = 'C';
    const ALIGN_RIGHT       = 'R';

    const V_ALIGN_TOP       = 'T';
    const V_ALIGN_MIDDLE    = 'M';
    const V_ALIGN_BOTTOM    = 'B';

    const TYPE_PNG          = 'png';
    const TYPE_JPEG         = 'jpg';

    /**
     * @var - jpg/png
     */
    private static $type;

    /**
     * @var int
     */
    private static $x = 0;

    /**
     * @var int
     */
    private static $y = 12;

    /**
     * @var int
     */
    private static $width = 128;

    /**
     * @var int
     */
    private static $height = 128;

    /**
     * @var bool
     */
    private static $transparent = false;

    /**
     * @var array
     */
    private static $background = array('R'=>255, 'G'=>255, 'B'=>255);

    /**
     * @var string
     */
    private static $text = 'Empty';

    /**
     * @var int
     */
    private static $angle = 0;

    /**
     * @var int
     */
    private static $lineSpacing = 12;

    /**
     * @var
     */
    private static $fontFile;

    /**
     * @var int
     */
    private static $fontSize = 12;

    /**
     * @var array
     */
    private static $fontColour = ['R' => 17, 'G' => 17, 'B' => 17];

    /**
     * @var array
     */
    private static $fontShadow = array();

    /**
     * @var - left/right/center
     */
    private static $align;

    /**
     * @var
     */
    private static $vlign;

    /**
     * @var int
     */
    private static $pngQuality = 8;

    /**
     * @var int
     */
    private static $jpegQuality = 80;

    /**
     * @var string
     */
    private static $destination = 'browser';

    /**
     * @var \Exception
     */
    public static $exception;


    /**
     * @param $text
     */
    public static function setText($text)
    {
        self::$text = $text;
    }

    /**
     * @param $space
     */
    public static function setLineSpacing($space)
    {
        self::$lineSpacing = intval($space);
    }

    /**
     * @param $x
     */
    public static function setX($x)
    {
        self::$x = $x;
    }

    /**
     * @param $y
     */
    public static function setY($y)
    {
        self::$y = $y;
    }

    /**
     * @param $width
     */
    public static function setWidth($width)
    {
        self::$width = $width;
    }

    /**
     * @param $height
     */
    public static function setHeight($height)
    {
        self::$height = $height;
    }

    /**
     * @param $angle
     */
    public static function setAngle($angle)
    {
        self::$angle = $angle;
    }

    /**
     * @param string $align
     */
    public static function align($align = self::ALIGN_LEFT)
    {
        self::$align = $align;
    }

    /**
     * @param string $align
     */
    public static function vAlign($align = self::V_ALIGN_TOP)
    {
        self::$vlign = $align;
    }

    /**
     * @param $file
     * @throws \Exception
     */
    public static function setFontFile($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("font file not found: $file");
        }

        self::$fontFile = $file;
    }

    /**
     * @param $size
     */
    public static function setFontSize($size)
    {
        self::$fontSize = intval($size);
    }

    /**
     * @param $colour
     * @param bool $opacity
     */
    public static function setFontColour($colour, $opacity = false)
    {
        self::$fontColour = self::hex2rgba($colour, $opacity);
    }

    /**
     * @param $colour
     * @param int $space
     * @param bool $opacity
     */
    public static function setFontShadow($colour, $space = 0, $opacity = false)
    {
        $rgba           = self::hex2rgba($colour, $opacity);
        $rgba['space']  = intval($space);

        self::$fontShadow = $rgba;
    }

    /**
     * @param $background   - Path to image file or a hex colour
     * @param bool $opacity - Background opacity
     */
    public static function setBackground($background, $opacity = false)
    {
        if (!is_file($background)) {

            self::$background = self::hex2rgba($background, $opacity);
        } else {

            self::$background = $background;
        }
    }

    /**
     * Set transparent background
     *
     * @param bool $transparent
     */
    public static function setTransparent($transparent)
    {
        self::$transparent = boolval($transparent);
    }

    /**
     * @param string $type
     * @param null $text
     * @param string $destination
     * @param int $quality
     */
    public static function create($type = self::TYPE_PNG, $text = null, $destination = 'browser', $quality =  8)
    {

        self::$destination = $destination;

        if ($type == self::TYPE_JPEG) {

            self::$type = self::TYPE_JPEG;
            self::$jpegQuality = intval($quality);
        } else {

            self::$type = self::TYPE_PNG;
            self::$pngQuality = intval($quality);
        }

        if(is_null($text)) {
            $text = self::$text;
        }

        self::make($text);
    }

    /**
     * @param $text
     */
    private function make($text)
    {
        try {

            if (is_string(self::$background) && is_file(self::$background)) {

                // create using image background

                $bgImageType = substr(self::$background, -3);

                if ($bgImageType == self::TYPE_PNG) {

                    $im = imagecreatefrompng(self::$background);
                } else if ($bgImageType == self::TYPE_JPEG) {

                    $im = imagecreatefromjpeg(self::$background);
                } else {
                    throw new \Exception("Invalid background extension, expecting .png or .jpg", 1);
                }

            } else if (self::$transparent) {

                // create image with transparent background

                $im = imagecreatetruecolor(self::$width, self::$height);

                imagealphablending($im, false);
                $transparency = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagefill($im, 0, 0, $transparency);
                imagesavealpha($im, true);

            } else {

                // create image from scratch
                $im = imagecreatetruecolor(self::$width, self::$height);

                if (!isset(self::$background['A'])) {

                    $background = imagecolorallocate(
                        $im, self::$background['R'], self::$background['G'], self::$background['B']
                    );
                } else {

                    $background = imagecolorallocatealpha(
                        $im,
                        self::$background['R'],
                        self::$background['G'],
                        self::$background['B'],
                        self::$background['A']
                    );
                }

                imagefilledrectangle($im, 0, 0, self::$width, self::$height, $background);
            }

            // add text
            if (is_string($text)) {
                $text = array($text);
            }

            foreach ($text as $line => $content) {

                self::addTextLine($im, $line, $content);
            }

            //render image
            self::render($im);

        } catch (\Exception $e) {

            self::$exception = $e;
        }
    }

    /**
     * @param $im
     * @param $line
     * @param $text
     */
    private static function addTextLine(&$im, $line, $text)
    {
        // find the size of the text
        $box = imagettfbbox(self::$fontSize, self::$angle, self::$fontFile, $text);

        // find the size of the image
        $xi = imagesx($im);
        $yi = imagesy($im);

        // text alignment
        switch(self::$align) {

            case self::ALIGN_CENTER:
                $xr         = abs(max($box[2], $box[4]));
                self::$x    = intval(($xi - $xr) / 2);
                break;

            case self::ALIGN_RIGHT:
                $textWidth  = abs($box[4] - $box[0]);
                self::$x    = $xi - $textWidth;
                break;
        }

        // text v alignment
        switch (self::$vlign) {

            case self::V_ALIGN_MIDDLE:
                $yr         = abs(max($box[5], $box[7]));
                self::$y    = intval(($yi + $yr) / 2)-2;
                break;

            case self::V_ALIGN_BOTTOM:
                $yr         = abs(max($box[5], $box[7]));
                self::$y    = self::$height-$yr;
                break;
        }

        // add the text
        if (!isset(self::$fontColour['A'])) {

            $textColour = imagecolorallocate(
                $im, self::$fontColour['R'], self::$fontColour['G'], self::$fontColour['B']
            );
        } else {

            // add with opacity
            $textColour = imagecolorallocatealpha(
                $im, self::$fontColour['R'], self::$fontColour['G'], self::$fontColour['B'], self::$fontColour['A']
            );
        }

        if ($line == 0) {

            // first line of text

            // text shadow
            if (!empty(self::$fontShadow)) {

                // add some shadow to the text
                self::applyFontShadow($im, $text, (self::$y + self::$fontShadow['space']));
            }

            imagettftext(
                $im, self::$fontSize, self::$angle, self::$x, self::$y, $textColour, self::$fontFile, $text
            );

        } else {

            // text shadow
            if (!empty(self::$fontShadow)) {

                // add some shadow to the text
                self::applyFontShadow(
                    $im, $text, ((self::$y + (self::$fontSize + self::$lineSpacing)*$line)+ self::$fontShadow['space'])
                );
            }

            imagettftext(
                $im,
                self::$fontSize,
                self::$angle,
                self::$x,
                (self::$y + (self::$fontSize + self::$lineSpacing)*$line),
                $textColour,
                self::$fontFile,
                $text
            );
        }
    }

    /**
     * @param $im
     * @param $text
     * @param $y
     */
    private static function applyFontShadow(&$im, $text, $y)
    {
        // add some shadow to the text
        $fontShadow = imagecolorallocate(
            $im, self::$fontShadow['R'], self::$fontShadow['G'], self::$fontShadow['B']
        );

        imagettftext(
            $im,
            self::$fontSize,
            self::$angle,
            self::$x + self::$fontShadow['space'],
            $y,
            $fontShadow,
            self::$fontFile,
            $text
        );
    }

    /**
     * @param $im
     */
    private static function render(&$im)
    {

        if (self::$destination == 'browser') {

            if (self::$type == self::TYPE_PNG) {

                header( "Content-type: image/png" );
                imagepng($im, null, self::$pngQuality);
            } else if (self::$type == self::TYPE_JPEG) {

                header( "Content-type: image/jpg" );
                imagejpeg($im, null, self::$jpegQuality);
            }

            imagedestroy($im);
            exit;

        } else {

            if (self::$type == self::TYPE_PNG) {

                imagepng($im, self::$destination, self::$pngQuality);
            } else if (self::$type == self::TYPE_JPEG) {

                imagejpeg($im, self::$destination, self::$jpegQuality);
            }

            imagedestroy($im);
            exit;
        }
    }

    /**
     * @param  string  $color    - hex color eg. #000000
     * @param  bool|int $opacity - 0 to 127
     * @return array
     */
    private static function hex2rgba($color, $opacity = false) {

        $default = array(
            'R' => 0,
            'G' => 0,
            'B' => 0
        );

        // return default if no colour provided
        if(empty($color)) {
            return $default;
        }

        // sanitize $colour if "#" is provided
        if ($color[0] == '#' ) {
            $color = substr( $color, 1 );
        }

        // check if colour has 6 or 3 characters and get values
        if (strlen($color) == 6) {

            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {

            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {

            return $default;
        }

        // convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){

            if(abs($opacity) > 127) {

                $opacity = 127;
            } else if (abs($opacity) < 0) {

                $opacity = 0;
            }

            $output = array_merge($rgb, array($opacity));
        } else {
            $output = $rgb;
        }

        //Return rgb(a) colour string
        $response = array(
            'R' => $output[0],
            'G' => $output[1],
            'B' => $output[2]
        );

        if ($opacity) {
            //alpha
            $response['A'] = $opacity;
        }

        return $response;
    }
} 