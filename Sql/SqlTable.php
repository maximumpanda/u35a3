<?php

/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 5/9/2017
 * Time: 2:37 PM
 */
class SqlTable
{
    public $Name;
    public $PrimaryKey;
    Public $Members = [];

    function __construct($name, $key = "", $members = [])
    {
        $this->Name = $name;
        $this->PrimaryKey = $key;
        $this->Members = $members;
    }

    public function Print(){
        print "<ul style='list-style-type: none'>{$this->Name} [";
        print "<ul> Key: {$this->PrimaryKey} </ul>";
        print "<ul>[</ul>";
        Helper::PrintArray($this->Members);
        /*foreach ($this->Members as $val){
            $val->Print();
        }*/
        print "]</ul>";
    }
}