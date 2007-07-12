<?php
class Vpc_Formular_Select_Index extends Vpc_Abstract
{
    function getTemplateVars($mode)
    {
        $row = $this->_getDbRow();
        if ($row) {       
            $size = $row->size;
            $name = $row->name;
           if($row->multiple == 1) {
                $multiple = "multiple";
           }
        } else {            
            $size = 10;
            $multiple = "";
            $name = "select";
        }
        $this->getOptions($name);
       
        $return['multiple'] = $multiple;
        $return['size'] = $size;
        $return['name'] = $name;
        $return['options'] = $this->getOptions($name);
        $return['template'] = 'Select.html';
        return $return;
    }
    
    private function getOptions ($name){
        $db = Zend_Registry::get('db');
        $sql = "SELECT * FROM `component_formular_select_options` WHERE `select_name` = '$name'";
        $options = $db->fetchAll($sql);
        $optionsString = "";
        foreach ($options as $row){          
          //  p ($row);
            $cnt = 0;
            foreach ($row as $value => $data){
                if ($cnt == 1)
                 $optionsString .= "<option> $data </option>";
                
                $cnt++;
            }
        }
		return $optionsString;
    }
}