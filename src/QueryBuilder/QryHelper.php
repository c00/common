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

    /** Turns   table.end - table.start   into   `table`.`end` - `table`.`start`
     * @param $string string The string to encapsulate
     * @return string
     */
    public static function encapStringWithOperators($string){
        $split = explode(' ', $string);
        $operators = ['-', '+', '*', '/'];

        $resultArray = array_map(function($part) use ($operators){
            if (in_array($part, $operators)) {
                return $part;
            } else {
                return self::encap($part);
            }
        }, $split);

        return implode(' ', $resultArray);
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