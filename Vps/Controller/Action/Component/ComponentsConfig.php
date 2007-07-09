<?php
class Vps_Controller_Action_Component_ComponentsConfig extends Vps_Controller_Action_AutoForm
{
    protected $_formFields = array();
    protected $_formButtons = array('save'   => true);
    protected $_formTableName = 'Vps_Dao_Vpc';
    
    public function init()
    {
        parent::init();
        $id = $this->_getParam('id');

        $row = $this->_formTable->find($id)->current();
        $className = str_replace('Index', 'Setup', $row->component_class);
        $this->_formFields = call_user_func(array($className, 'getConfigParams'));
    }
    
    public function jsonLoadAction()
    {
        $this->view->data = array();
        foreach ($this->_formFields as $field) {
            if(isset($field['name'])) {
                $this->view->data[$field['name']] = $this->_formTable->getParamValue($this->_getParam('id'), $field['name']);
            }
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }
    }

}
