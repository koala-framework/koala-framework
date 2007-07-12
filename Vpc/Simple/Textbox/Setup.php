<?php
class Vpc_Simple_Textbox_Setup extends Vpc_Setup_Abstract 
{
    
    //habe hier das static entfernt .. problem
    public function setup()
    {        
        $fields['content'] = 'text NOT NULL';
        $this->createTable('component_textbox', $fields);       
    }
}