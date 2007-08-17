<?php
class Vpc_Formular_Select_Index extends Vpc_Formular_Field_Decide_Abstract
{
    protected $_settings = array(
        'rows' => '10',
        'name' => ''
    );
    protected $_tablename = 'Vpc_Formular_Select_IndexModel';
    public $controllerClass = 'Vpc_Formular_Select_IndexController';
    const NAME = 'Formular.Select';

    public function getTemplateVars()
    {
        $where = array(
            'page_id = ?' => $this->getDbId(),
            'component_key = ?' => $this->getComponentKey()
        );
        $options = $this->_getTable('Vpc_Formular_Select_OptionsModel')->fetchAll($where);

        $return = parent::getTemplateVars();
        $return['rows'] = $this->getSetting('rows');
        $return['name'] = $this->getSetting('name');
        $return['options'] = $options->toArray();
        $return['template'] = 'Formular/Select.html';
        return $return;
    }

}