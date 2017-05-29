<?php

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 5/29/2017
 * Time: 2:01 PM
 */
class FormObjectFormElement
{
    public $Name;
    public $DefaultValue;
    public $InputType;
    public $Values;
    public $Enabled = true;
    private $_source;

    public static $InputTypes = [
        0 => 'text',
        1 => 'number',
        2 => 'select',
        3 => 'date',
        4 => 'checkbox'
    ];

    public function __construct(SqlType $object)
    {
        $this->Name = $object->Name;
        $this->_source = $object;
        if($object->KeyType = 1) $this->Enabled = false;
        $this->DefaultValue = $object->Value;
        $this->InputType = FormObjectFormElement::$InputTypes[$this->ParseInputType($object)];
    }

    public function BuildHtml(){
        print "<input type='$this->InputType' name='$this->Name' maxlength='$this->_source->Length' value='$this->DefaultValue'><br>";
    }

    private function ParseInputType(SqlType $object){
        if ($object->KeyType == 2) return 2;
        if (strpos($object->KeyType, 'int') !== false){
            if ($object->KeyType == "tinyint") {
                if ($object->Length == 1) return 4;
            }
            return 1;
        }
        if (strpos($object->KeyType, "char")!== false){
            return 0;
        }
        if (strpos($object->KeyType, 'date') !== false){
            return 3;
        }
        return 0;
    }

}