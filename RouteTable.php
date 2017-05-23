<?php
include_once $_SERVER["DOCUMENT_ROOT"] ."/MasterController.php";
class RouteTable
{

    public static $Routes = [];
    public static $HiddenBranches = [
        "Api",
        "Error",
    ];

    public static function ValidatePath($path){
        $result = RouteTable::CheckPathToDestination($path);
        if ($result == -1) {
            Router::ReDirectError(404);
    }
        if ($result == 0){
            Router::ReDirectIncomplete($path);
        }
        return $result;
    }

    public static function CheckPathToDestination($path){
        $current = self::$Routes;
        $count = count($path);
        Helper::PrintArray($path);
        for ($i = 0; $i < $count; $i++) {
            Helper::PrintArray($current);
            if (isset($current[strtolower($path[$i])])){
                if ($i+1 >= $count){
                    return 0;
                }
                if (self::CheckMethodExists($current, $path[$i], $path[$i+1])){
                    return 1;
                }
                $current = $current[$path[$i]];
            }
        }
        return -1;
    }

    private static function CheckMethodExists($array, $controllerKey, $viewKey){
        $requestMethod = Helper::GetRequestMethod();
        if (isset($array[$controllerKey][$requestMethod][$viewKey])){
            return true;
        }
        return false;
    }

    public static function GenerateRouteTable($controllers){
        $table = [];
        foreach ($controllers as $controller){
            $table = array_merge($table, self::GenerateRouteTableElement($controller));
        }
        self::$Routes = $table;
    }

    private static function GenerateParentList($controllerPath){
        $lastDot = strrpos($controllerPath, ".");
        $prefixLength =  strlen($_SERVER['DOCUMENT_ROOT'] . "/Controllers/");
        $relativePath = substr($controllerPath, $prefixLength , $lastDot - $prefixLength);
        $list = array_filter(explode("/", $relativePath));
        array_pop($list);
        $res = [];
        foreach ($list as $key => $value){
            $res[$key] = strtolower($value);
        }
        return $res;
    }

    private static function GenerateRouteTableElement($controller){
        $parentList = self::GenerateParentList($controller);
        $controllerName = Helper::GetClassName($controller);
        $base = self::GetControllerBaseName($controllerName);
        $methods = (new ReflectionClass($controllerName))->getMethods(ReflectionMethod::IS_PUBLIC);
        $gets = [];
        $posts = [];
        $element = [];
        foreach ($methods as $method){
            if ( strpos($method->name, "Get") !== false) $gets[substr($method->name, 3)] = $method->name;
            if (strpos($method->name, "Post") !== false) $posts[substr($method->name, 4)] = $method->name;
        }
        $element[$base] =[
            "Controller" => $controllerName,
            "Get" => $gets,
            "Post" => $posts
        ];
        $parentListSize = count($parentList);
        for ($i = $parentListSize-1; $i >= 0; $i--){
            $newElement = [ $parentList[$i] => $element];
            $element = $newElement;
        }
        return $element;
    }

    private static function GetControllerBaseName($name){
        $controllerText = strpos($name, "Controller");
        return substr($name, 0, $controllerText);
    }
}