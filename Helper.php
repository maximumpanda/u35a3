<?php

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 4/25/2017
 * Time: 3:21 PM
 */
class Helper
{
    public static function Print($message){
        print ("<div>" . $message . "</div>");
    }

    public static function PrintArray($array){
        print ("<pre>" . print_r($array, true) . "</pre>");
    }

    public static function GetClassName($filename) {
        $lastSlash = strrpos($filename, "/");
        if ($lastSlash == false) return false;
        $lastDot = strrpos($filename, ".");
        if ($lastDot == false) return false;
        return substr($filename, $lastSlash + 1, $lastDot - $lastSlash -1);
    }
}