<?php
/**
 * Created by PhpStorm.
 * User: c00yt
 * Date: 04/12/2016
 * Time: 14:44
 */

namespace c00\QueryBuilder;


class QryHelper
{

    /** Turns   table.column   into   `table`.`column`
     * @param $string
     * @return string
     */
    public static function encap($string)
    {
        $string = str_replace('`', '', $string);

        $parts = explode('.', $string);
        foreach ($parts as &$part) {
            if ($part == '*') continue;
            $part = "`$part`";
        }
        $encapsulated = implode('.', $parts);

        return $encapsulated;
    }

    /** Turns   table.column   into   `table`.`column`
     * @param $array array takes an array of strings.
     * @return array
     */
    public static function encapArray($array)
    {
        foreach ($array as &$item) {
            $item = QryHelper::encap($item);
        }


        return $array;
    }
}