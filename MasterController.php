<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 4/25/2017
 * Time: 11:00 AM
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/Sql/Sql.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Session.php";


class MasterController{
    private $Path = [];
    private $controllers;

    function __construct($path)
    {
        $this->GetControllers();
        $this->controllers = glob($_SERVER['DOCUMENT_ROOT'] . "/Controllers/*.php");
        foreach ($this->controllers as $file){
            include_once $file;
        }
        $this->Path = $path;
        RouteTable::$Routes = $this->GenerateRouteTable();
        $this->BuildView();
    }

    private function BuildView(){
        if (end($this->Path)[0] == "?"){
            Session::SetParams(end($path));
            array_pop($this->Path);
        }
        if ($this->Path == "") {
            $objData = RouteTable::$DefaultPath;
        }
        else {
            $objData = RouteTable::PathToDestination($this->Path);
        }
        if ($objData == null) {
            $objData = RouteTable::$DefaultErrorPath;
        }
        $this->Path = $objData;

        $this->CallController();
        Session::SetView($this->Path);
    }

    public function GenerateRouteTable(){
        $table = [];
        foreach ($this->controllers as $controller){
            $this->GenerateRouteTableElement($controller);
        }
        Helper::PrintArray($table);
        return $table;
    }

    private function GenerateParentList($controllerPath){
        $lastDash = strrpos($controllerPath, "/");
        $prefixLength =  strlen($_SERVER['DOCUMENT_ROOT'] . "/Controllers/");
        $relativePath = substr($controllerPath, $prefixLength , $lastDash - $prefixLength);
        return array_filter(explode("/", $relativePath));
    }

    private function GenerateRouteTableElement($controller){
        $parentList = $this->GenerateParentList($controller);
        $controllerName = Helper::GetClassName($controller);
        $base = $this->GetControllerBaseName($controllerName);
        $methods = get_class_methods($controllerName);
        $gets = [];
        $posts = [];
        $element = [];
        foreach ($methods as $methodName){
            if ( strpos($methodName, "Get") !== false) array_push($gets, substr($methodName, 3));
            if (strpos($methodName, "Post") !== false) array_push($posts, substr($methodName, 4));
        }
        if ($parentList != null){
            $last = $element;
            foreach ($parentList as $parent){
                $last[$parent] = [];
                $last = $last[$parent];
            }
            $last[$base] =[
                "Controller" => $controllerName,
                "Get" => $gets,
                "Post" => $posts
            ];
            return $element;
        }
        return false;
    }

    private function GetControllerBaseName($name){
        $controllerText = strpos($name, "Controller");
        return substr($name, 0, $controllerText);
    }

    private function GetControllers(){
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/Controllers/";
        $controllers = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $path){
            if (strpos($path, ".php")) array_push($controllers, $path);
        }
            Helper::PrintArray($controllers);
    }

    private function CallController(){
        $count = count($this->Path);
        Helper::Print($count);
        Helper::PrintArray($this->Path);
        $controller = $this->Path[$count-2] . "Controller";
        Helper::Print($controller);
        call_user_func($controller."::".$this->Path[$count-1]);
    }
}