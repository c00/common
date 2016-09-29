<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 19/07/2016
 * Time: 15:32
 */

namespace c00\common;


class Image
{
    private $file;

    public static function newInstance($file)
    {
        $i = new Image();
        $i->file = $file;
    }

    public function resize_image($w, $h, $crop = false) {
        //https://stackoverflow.com/questions/14649645/resize-image-in-php

        list($width, $height) = getimagesize($this->file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($this->file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        imagejpeg($dst, '/some/new/path');

        return $dst;
    }
}