<?php

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 4/25/2017
 * Time: 3:31 PM
 */
class ErrorController
{
    public static function GetIndex(){
        Session::$Bag["Title"] = "404";
        Session::$Bag["Message"] = "Page Not Found";
    }
}