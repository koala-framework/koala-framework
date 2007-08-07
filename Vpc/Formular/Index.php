<?php
class Vpc_Formular_Index extends Vpc_Paragraphs_Abstract
{
    protected $_tablename = 'Vpc_Formular_IndexModel';
    public $controllerClass = 'Vpc_Formular_IndexController';
    const NAME = 'Formular.Formular';

    private $_errors = array();
    private $_fields = array();
    private $_components = array();
    public function getTemplateVars()
    {
        if ($_POST != array()) {
            if ($this->_validateFields()) {
                d("Formular wurde abgeschickt");

            } else {
                p("Fehler in der Eingabe");
            }
        } else {
            $this->getChildComponents();
        }

        $vars = parent::getTemplateVars();
        $vars['action'] = $_SERVER['REQUEST_URI'];
        $vars['errors'] = $this->_errors;
        $vars['names'] = $this->_fields;
        $vars['id'] = $this->getDbId().$this->getComponentKey();
        $vars['upload'] = $this->_checkUpload();
        $vars['submit'] = $this->_checkIfSubmit();
        $vars['template'] = 'Formular.html';
        return $vars;
    }





    /**
     * Holt die Formularfelder und setzt den Namen für das jeweilige Feld auf
     * Basis der Formulartabelle -> es wird ein eintrag in die Datenbank vorgenommen
     */
    public function getChildComponents() {
    	if ($this->_components) {
    		return $this->_components;
    	}
        $fields = array();
        $names = array();
        $components = array();

        foreach ($this->_getData() as $row){
            $filter = new Zend_Filter_Alpha();
            $newName = $filter->filter($row->name);
            $tempName = $newName;
            $cnt = 1;
            while (in_array($newName, $names)){
                $newName = $tempName.$cnt;
                $cnt++;
            }

            $names[] = $newName;
	        $component = $this->createComponent($row->component_class, $row->id);

            if ($component instanceof Vpc_Formular_Field_Interface ) {
                $component->setName($newName);
                $component->setErrorField($row->name);
                //$this->_components[$component->getComponentKey()] = $component;

            }
             $this->_components[$component->getComponentKey()] = $component;
            //TODO nachfragen ob das so gemacht werden darf
            $fields[] = array ('name' => $row->name, 'id' => ($row->page_id.'-'.$row->id), 'mandatory' => $row->mandatory, 'noCols' => $row->no_cols, 'isValid' => 1);
        }
        //soll hier nur einmmal aufgerufen werden
        if ($this->_fields == array())$this->_fields = $fields;
        return $this->_components;
    }


    /*private function _checkForm()
    {

        if (!$this->_checkIfSubmit()) {

            $submit = $this->createComponent('Vpc_Formular_Submit_Index', $this->getComponentId(), 2);
            $submit->setSetting('name', 'Absenden');
            $submit->setSetting('value', 'Absenden');
            $this->addChildComponent($submit);
        }
    }*/

    private function _checkIfSubmit()
    {
        $this->_components = $this->getChildComponents();

        foreach($this->_components as $value => $component) {
            if ($component instanceof Vpc_Formular_Submit_Index) {
                return true;
            }
        }
        return false;
    }

    private function _checkUpload()
    {
        $this->_components = $this->getChildComponents();

        foreach($this->_components as $value => $component) {
            if ($component instanceof Vpc_Formular_FileUpload_Index) {
                return true;
            }
        }
        return false;
    }

    private function _validateFields()
    {
    	//$this->getChildComponents();

        $this->_fields;
        $return = true;

        $components = array();


        $components = $this->getChildComponents();

        foreach($this->_components as $value => $component) {
            if ($component instanceof Vpc_Formular_Field_Interface) {
            	$id = str_replace('-', '', $component->getComponentKey());
                $row = $this->_getTable()->fetchAll(array('page_id = ?'  => $component->getDbId(),
            										      'id = ?'       => $id))->current();
                $component->processInput();

                if ($component->validateField($row->mandatory) !== true) {
                    $return = false;
                    $this->_errors[] = $component->validateField($row->mandatory);
                    $this->_notValid($component->getDbId().'-'.$id);
                }
            }
            $components[$value] = $component;
        }
        $this->_components = $components;
        return $return;
    }

    private function _notValid ($id){
        foreach ($this->_fields AS $fieldkey => $field){
            if ($field['id'] == $id){
                $field['isValid'] = 0;
            }
            $this->_fields[$fieldkey] = $field;

        }
    }
}


