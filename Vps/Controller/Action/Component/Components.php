<?php
class Vps_Controller_Action_Component_Components extends Vps_Controller_Action
{
	public function indexAction()
	{
		$filename = "application/components.ini";    
		$components = new Vps_Config_Ini($filename);
		
		foreach ($components as $component => $compData) {
			try {
    			$config = call_user_func(array($component, 'getParams'));  
			} catch (Zend_Exception $e){
	    		p("Component $component was not found");
			}
			
			foreach ($config as $element => $value){
				if (!$components->checkKeyExists($component, $element)) {
					$components->setValue($component, $element, (string) $value);		
					p("value changed -> " . $compData->$element);		
				} else {
    				p("key exists -> " . $element);
				}		
	        }            
	    }	
      
        $components->write();
    }
}
