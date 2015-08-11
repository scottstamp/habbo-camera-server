<?php

/**
 *      ,-. ,_, . . ,-. ,-. ,-. ,-. ,-,-. ,-. ,-. ,-.
 *      ,-|  /  | | |   |-' |   ,-| | | | |-' |   ,-|
 *      `-^ '"' `-^ '   `-' `-' `-^ ' ' ' `-' '   `-^
 * because a camera script need more than a good name.
 * @author Claudio A. Santoro Wunder
 * @version 1.0
 * @copyright Sulake Corporation Oy
 */

/**
 * @about Azure Camera is a PhP class, that emulates Sulake's Habbo Hotel "in-game camera", (that applet that is used for Selfies)
 * @about This script doesn't use any script or code-parts from Sulake Corporation Oy
 */

/*
    Azure Camera PhP GD API, a Graphical PhP Class to Generate Habbo`s Camera API Images.
    Copyright (C) 2015 Claudio Santoro
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

/**
 * Class CameraGD
 * @package AzureCamera
 * Main Class to Generate Image
 */
final class CameraGD
{
    /** @var array|mixed */
    private $json = [];
    /** @var resource */
    private $image;
    /** @var resource */
    private $image_small;

    /**
     * Do the Thing
     * @author sant0ro
     *
     * @param string $image_url
     * @param string $image_small_url
     * @param int $image_w
     * @param int $image_h
     * @param int $image_s_w
     * @param int $image_s_h
     */
    function __construct($image_url = '', $image_small_url = '', $image_w = 320, $image_h = 320, $image_s_w = 100, $image_s_h = 100)
    {
        /* let's get php input */
        if (is_null($this->json = json_decode(file_get_contents('php://input'), true)))
            echo $this->just_die('500', 'the php input, isn\'t a valid jSON data!', true);

        /* other prob y? */
        if ((!isset($this->json['roomid'])) || (!isset($this->json['timestamp'])))
            echo $this->just_die('403', 'the jSON doesn\'t contains timestamp || roomid', true);

        /* set the user-defined variables */
        $this->set_variables($image_url, $image_small_url, $image_w, $image_h, $image_s_w, $image_s_h);

        /* i love this song */
        echo $this->let_it_go();
    }

    /**
     * set the user defined variables of the CameraGD script.
     *
     * @param string $image_url
     * @param string $image_small_url
     * @param int $image_w
     * @param int $image_h
     * @param int $image_s_w
     * @param int $image_s_h
     */
    private function set_variables($image_url = '', $image_small_url = '', $image_w = 320, $image_h = 320, $image_s_w = 100, $image_s_h = 100)
    {
        /* let's do it now! */
        $image_url       = str_replace('[IMAGE_URL]', ($this->json['roomid'] . '-' . $this->json['timestamp']), $image_url);
        $image_small_url = str_replace('[IMAGE_URL]', ($this->json['roomid'] . '-' . $this->json['timestamp']), $image_small_url);

        /* define folder-path variables */
        defined('ROOT_DIR') || define('ROOT_DIR', __DIR__);

        /* camera main image sizes */
        defined('IMAGE_W') || define('IMAGE_W', $image_w);
        defined('IMAGE_H') || define('IMAGE_H', $image_h);

        /* camera thumbnail image sizes */
        defined('IMAGE_S_W') || define('IMAGE_S_W', $image_s_w);
        defined('IMAGE_S_H') || define('IMAGE_S_H', $image_s_h);

        /* server-camera root dir (output for generated images) */
        defined('SERVER_CAMERA') || define('SERVER_CAMERA', 'servercamera');

        /* set requester data */
        $client  = $_SERVER['HTTP_CLIENT_IP'];
        $forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        /* validate hotel requester country */
        defined('HOTEL_COUNTRY') || define('HOTEL_COUNTRY', $this->visitor_country($client, $forward, $remote));

        $image_url       = str_replace('[SERVER_CAMERA]', SERVER_CAMERA, $image_url);
        $image_small_url = str_replace('[SERVER_CAMERA]', SERVER_CAMERA, $image_small_url);

        $image_url       = str_replace('[HOTEL_COUNTRY]', HOTEL_COUNTRY, $image_url);
        $image_small_url = str_replace('[HOTEL_COUNTRY]', HOTEL_COUNTRY, $image_small_url);

        /**
         * you need habbo avatars, furniture, effects, and pets sprites extracted manually from all SWF's.
         * you can get, extract all seeing the habbo-asset-extractor repository.
         * @link http://github.com/sant0ro/habbo-asset-extractor/
         * @author Claudio Santoro
         * @package habbo-asset-extractor
         */
        defined('SPRITES_ROOT') || define('SPRITES_ROOT', ROOT_DIR . '/sprites/');
        defined('MASKS_ROOT') || define('MASKS_ROOT', ROOT_DIR . '/masks/');

        /* define image url-variables */

        /* camera main image url */
        defined('IMAGE_URL') || define('IMAGE_URL', $image_url);

        /* camera thumbnail image url */
        defined('IMAGE_SMALL_URL') || define('IMAGE_SMALL_URL', $image_small_url);
    }

    /**
     * get visitors country code
     * @indeed visitors in this case need to bet the Habbo Client requesting the Camera..
     * @indeed If this not happen you have security Violation
     * @see Adobe's Flash X-CORS HTTP Request Policy (crossdomain.xml)
     * @link http://www.adobe.com/devnet/adobe-media-server/articles/cross-domain-xml-for-streaming.html
     *
     * @param string $client HTTP_CLIENT_IP
     * @param string $forward HTTP_X_FORWARDED_FOR
     * @param string $remote REMOTE_ADDR
     * @return string
     */
    private function visitor_country($client = '', $forward = '', $remote = '')
    {
        /* filter ip data, check if is valid, and get geoplugin ip data */
        $ip      = ((filter_var($client, FILTER_VALIDATE_IP)) ? $client : ((filter_var($forward, FILTER_VALIDATE_IP)) ? $forward : $remote));
        $ip_data = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=$ip"));
        $result  = (($ip_data && $ip_data->geoplugin_countryName != null) ? $ip_data->geoplugin_countryName : 'US');

        /* return country code */
        return geoip_country_code_by_name($result);
    }

    /**
     * let it go
     * start all tha xit
     */
    private function let_it_go()
    {
        /* let allocate evrathing */
        $this->image = imagecreatetruecolor(IMAGE_W, IMAGE_H);

        /* render all jSON planes */
        $this->render_planes();

        /* render all jSON sprites */
        $this->render_sprites();

        /* create thumbnail image */
        $this->smaller_image();

        /* save the image */
        $this->image_create();

        /* i must give back the url of main image because CLIENT need's */
        return IMAGE_URL;
    }

    /**
     * Converts a Hex String into RGB
     * @param string $hex Input Hex
     * @return array RGB String
     */
    private function hex_to_rgb($hex = '')
    {
        // remove the hex identifier tag
        $hex = str_replace('#', '', $hex);

        // replaces the correspondent hex value
        $r = ((strlen($hex) == 3) ? hexdec(substr($hex, 0, 1) . substr($hex, 0, 1)) : hexdec(substr($hex, 0, 2)));
        $g = ((strlen($hex) == 3) ? hexdec(substr($hex, 1, 1) . substr($hex, 1, 1)) : hexdec(substr($hex, 2, 2)));
        $b = ((strlen($hex) == 3) ? hexdec(substr($hex, 2, 1) . substr($hex, 2, 1)) : hexdec(substr($hex, 4, 2)));

        // return rgb array
        return [$r, $g, $b];
    }

    /**
     * Colorize a image part set Image
     * @param resource $image Resource Link
     * @param integer $red R-GB
     * @param integer $green R-G-B
     * @param integer $blue RG-B
     */
    private function image_recolor(&$image, $red = 255, $green = 255, $blue = 255)
    {
        // get image width and height
        $width  = imagesx($image);
        $height = imagesy($image);

        // recolor every pixel
        for ($x = 0; $x < $width; $x++):
            for ($y = 0; $y < $height; $y++):

                // get rgb of the pixel
                $rgb = imagecolorsforindex($image, (imagecolorat($image, $x, $y)));

                // put the rgb color
                $r = (($red / 255) * ($rgb['red']));
                $g = (($green / 255) * ($rgb['green']));
                $b = (($blue / 255) * ($rgb['blue']));

                // set the new pixel
                imagesetpixel($image, $x, $y, (imagecolorallocatealpha($image, $r, $g, $b, ($rgb['alpha']))));
            endfor;
        endfor;
    }

    /**
     * An Old but Needed function for the Actual Days of PhP
     * @source http://forums.devnetwork.net/viewtopic.php?f=1&t=103330#p553333
     * @author RedMonkey
     * Edited by AzureTeam
     *
     * @param $dst
     * @param $src
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $w
     * @param $h
     * @param $pct
     */
    private function image_copy_merge_with_alpha($dst, $src, $dst_x = 0, $dst_y = 0, $src_x = 0, $src_y = 0, $w = 0, $h = 0, $pct = 100)
    {
        /* yes divide */
        $pct /= 100;

        /* make sure opacity level is within range before going any further */
        $pct = max(min(1, $pct), 0);

        /* work out if we need to bother correcting for opacity */
        if ($pct < 1):
            /* we need a copy of the original to work from, only copy the cropped */
            /* area of src                                                        */
            $src_copy = imagecreatetruecolor($w, $h);

            /* attempt to maintain alpha levels, alpha blending must be *off* */
            imagealphablending($src_copy, false);
            imagesavealpha($src_copy, true);

            imagecopy($src_copy, $src, 0, 0, $src_x, $src_y, $w, $h);

            /* we need to know the max transparency of the image */
            $max_t = 0;

            for ($y = 0; $y < $h; $y++):
                for ($x = 0; $x < $w; $x++):
                    $src_c = imagecolorat($src_copy, $x, $y);
                    $src_a = (($src_c >> 24) & 0xFF);
                    $max_t = (($src_a > $max_t) ? $src_a : $max_t);
                endfor;
            endfor;

            /* src has no transparency? set it to use full alpha range */
            $max_t = (($max_t == 0) ? 127 : $max_t);

            /* $max_t is now being reused as the correction factor to apply based */
            /* on the original transparency range of  src                         */
            $max_t /= 127;

            /* go back through the image adjusting alpha channel as required */
            for ($y = 0; $y < $h; $y++):
                for ($x = 0; $x < $w; $x++):
                    $src_c = imagecolorat($src, $src_x + $x, $src_y + $y);
                    $src_a = (($src_c >> 24) & 0xFF);
                    $src_r = (($src_c >> 16) & 0xFF);
                    $src_g = (($src_c >> 8) & 0xFF);
                    $src_b = (($src_c) & 0xFF);

                    /* alpha channel compensation */
                    $src_a = ((($src_a + 127) - (127 * $pct)) * $max_t);
                    $src_a = (($src_a > 127) ? 127 : (int)$src_a);

                    /* get and set this pixel's adjusted RGBA colour index */
                    $rgba = imagecolorallocatealpha($src_copy, $src_r, $src_g, $src_b, $src_a);

                    /* @method /imagecolorclosestalpha returns -1 for PHP versions prior  */
                    /* to 5.1.3 when allocation failed                               */
                    if (($rgba === false) || ($rgba == -1)) $rgba = imagecolorclosestalpha($src_copy, $src_r, $src_g, $src_b, $src_a);

                    imagesetpixel($src_copy, $x, $y, $rgba);
                endfor;
            endfor;

            /* call image copy passing our alpha adjusted image as src */
            imagecopy($dst, $src_copy, $dst_x, $dst_y, 0, 0, $w, $h);

            /* cleanup, free memory */
            imagedestroy($src_copy);
            return;
        endif;

        /* still here? no opacity adjustment required so pass straight through to */
        /* @method /imagecopy rather than @method /imagecopymerge to retain alpha channels          */
        imagecopy($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $w, $h);
        return;
    }

    /**
     * render all the planes of the fuckin xit ;)
     * @author TyrexFR
     * @author sant0ro
     * @todo: improve door math calculation
     * @bug: door position y is really bad.
     */
    protected function render_planes()
    {
        /* foreach each plane of the image */
        foreach ($this->json['planes'] as $plane):

            /* assign image assets resources */
            $color_rgb = $this->hex_to_rgb(dechex($plane['color']));
            $color     = imagecolorallocate($this->image, $color_rgb[0], $color_rgb[1], $color_rgb[2]);

            /* array with polygons */
            $polygon_array = [];

            /* put in the polygon array */
            array_push($polygon_array, $plane['cornerPoints'][0]['x'], $plane['cornerPoints'][0]['y'], $plane['cornerPoints'][1]['x'], $plane['cornerPoints'][1]['y'], $plane['cornerPoints'][3]['x'], $plane['cornerPoints'][3]['y'], $plane['cornerPoints'][2]['x'], $plane['cornerPoints'][2]['y']);

            /* is also a pokemon name. */
            imagefilledpolygon($this->image, $polygon_array, count($polygon_array) / 2, $color);

            /* get tex_cols of every plane */
            if (array_key_exists('texCols', $plane)):
                foreach ($plane['texCols'] as $tex_col):
                    /* sure that happend? */
                    if (empty($tex_col)) continue;

                    /* jingle */
                    if ((isset($tex_col['flipH'])) && (stripos('_flipH', $tex_col['assetNames'][0] !== false))) $mask['assetNames'] = str_ireplace('_flipH', '', ($tex_col['assetNames'][0]));

                    /* let's create a image.. */
                    $tex_cols_asset = imagecreatefrompng(MASKS_ROOT . $tex_col['assetNames'][0] . '.png');

                    /* soo flip, soo flip, flip. */
                    if (isset($mask['flipH']) && ($mask['flipH'] == 'true')) imageflip($tex_cols_asset, IMG_FLIP_HORIZONTAL);

                    /* really, tha color is really bad.. */
                    $this->image_recolor($tex_cols_asset, $color_rgb[0], $color_rgb[1], $color_rgb[2]);
                    imagesettile($this->image, $tex_cols_asset);

                    /* the texcols must be putted back into original image, y? */
                    imagefilledpolygon($this->image, $polygon_array, (count($polygon_array) / 2), IMG_COLOR_TILED);

                    /* no garbage, sir */
                    imagedestroy($tex_cols_asset);
                endforeach;
            endif;

            /* get masks of every plane */
            if (array_key_exists('masks', $plane)):
                foreach ($plane['masks'] as $mask):
                    /* sure that happend? */
                    if (empty($mask)) continue;

                    /* jingle */
                    if ((isset($mask['flipH'])) && (stripos('_flipH', $mask['name'] !== false))) $mask['name'] = str_ireplace('_flipH', '', ($mask['name']));

                    /* dingle bells.. */
                    $mask_asset = imagecreatefrompng(MASKS_ROOT . $mask['name'] . '.png');

                    /* soo flip, soo flip, flip. */
                    if (isset($mask['flipH']) && ($mask['flipH'] == 'true')) imageflip($mask_asset, IMG_FLIP_HORIZONTAL);

                    /* copy me please, and put me into original! */
                    imagecopy($this->image, $mask_asset, $plane['cornerPoints'][1]['x'] + $mask['location']['x'], $mask['location']['y'] * 3, 0, 0, imagesx($mask_asset), imagesy($mask_asset));

                    /* no garbage, sir */
                    imagedestroy($mask_asset);
                endforeach;
            endif;

            /* adele says, this is the end (jingle) */
        endforeach;
    }

    /**
     * Render all Image Sprites for the Camera.
     * @author TyrexFR
     * @author sant0ro
     * @todo: make this xit better
     * @todo: better sprites positioning
     */
    protected function render_sprites()
    {
        /* every sprite... avatars, furniture, soo.. everything */
        foreach ($this->json['sprites'] as $sprite):

            /* get out of there */
            $tha_sprite = imagecreatefrompng(SPRITES_ROOT . $sprite['name'] . '.png');

            /* i'm the alpha and omega, no, just alpha. */
            $alpha = ((isset($sprite['alpha'])) ? ((int)$sprite['alpha']) : 100);

            /* soo flip, soo flip, flip. */
            if (isset($sprite['flipH']) && ($sprite['flipH'] == 'true')) imageflip($tha_sprite, IMG_FLIP_HORIZONTAL);

            /* really, why Habbo use bad TrueColor Codes? */
            if (array_key_exists('color', $sprite) && ($sprite['color'] != '16777215')):
                $color_rgb = $this->hex_to_rgb(dechex($sprite['color']));
                $this->image_recolor($tha_sprite, $color_rgb[0], $color_rgb[1], $color_rgb[2]);
            endif;

            /* really, that is good! */
            $this->image_copy_merge_with_alpha($this->image, $tha_sprite, $sprite['x'], $sprite['y'], 0, 0, imagesx($tha_sprite), imagesy($tha_sprite), $alpha);

            /* really, we don't wanna garbage! */
            imagedestroy($tha_sprite);
        endforeach;
    }

    /**
     * let's just die, okay?
     * @author Claudio Santoro
     * @todo: make this better
     *
     * @param int $error_code
     * @param string $error_message
     * @param bool|false $must_die
     * @return string|void
     */
    private function just_die($error_code = 1, $error_message = '', $must_die = false)
    {
        $r = '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"><br><br><br><div class="row"><div class="col-sm-2"></div><div class="col-sm-8"><div class="alert alert-danger"><h4><i>Oh No!</i> (#' . $error_code . ')</h4><b> We really sorry, but the following error happend:</b><br><hr><blockquote><h5>' . $error_message . '</h5></blockquote></div></div></div>';
        return (($must_die) ? die($r) : $r);
    }

    /**
     * this method save in a physical link, the allocated memory from the image
     * @uses image/png compression
     * @see https://en.wikipedia.org/wiki/Portable_Network_Graphics
     * also this method erases the allocated memory
     */
    private function image_create()
    {
        /* check if the variables are really valid resources.. otherwise try to create from string.. */
        $this->image       = (((!is_resource($this->image)) && (is_string($this->image))) ? (string)imagecreatefromstring($this->image) : $this->image);
        $this->image_small = (((!is_resource($this->image_small)) && (is_string($this->image_small))) ? (string)imagecreatefromstring($this->image_small) : $this->image_small);

        /* save image alpha blending (only if in other steps didn't that)  */
        imagesavealpha($this->image, true);

        /* save main camera image */
        imagepng($this->image, IMAGE_URL);

        /* save the image thumbnail */
        imagepng($this->image_small, IMAGE_SMALL_URL);
    }

    /**
     * resize the camera image, for a smaller image
     * that will be the thumbnail
     * @author sant0ro
     */
    private function smaller_image()
    {
        /* why a function only with that? */
        $this->image_small = imagescale($this->image, IMAGE_S_W, IMAGE_S_H);
    }

    /**
     * destroy! all generated images
     */
    function __destruct()
    {
        /* because we love destroy memory */
        imagedestroy($this->image_small);
        imagedestroy($this->image);
    }
}

/**
 * available replaced variables:
 * [SERVER_CAMERA], [HOTEL_COUNTRY], [IMAGE_URL]
 */

new CameraGD(('[SERVER_CAMERA]/purchased/[HOTEL_COUNTRY]/[IMAGE_URL].png'), ('[SERVER_CAMERA]/purchased/[HOTEL_COUNTRY]/[IMAGE_URL].png'));
exit;
