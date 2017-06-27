<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 26/06/17
 * Time: 23:33
 */

namespace c00\sample;


class Box
{
    public $height;
    public $width;
    public $depth;

    public function __construct() { }

    public static function newInstance($height, $width, $depth)
    {
        $b = new Box();
        $b->height = $height;
        $b->width = $width;
        $b->depth = $depth;

        return $b;
    }
}