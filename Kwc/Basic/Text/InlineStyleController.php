<?php
class Kwc_Basic_Text_InlineStyleController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_formName = 'Kwc_Basic_Text_InlineStyleForm';

    public function init()
    {
        $class = $this->_getParam('componentClass');
        if (!Kwc_Abstract::getSetting($class, 'enableStyles') ||
            !Kwc_Abstract::getSetting($class, 'enableStylesEditor')
        ) {
            throw new Kwf_Exception("Styles are disabled");
        }
        $this->_model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($class, 'stylesModel'));
        parent::init();
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        if (!$row->tag) $row->tag = 'span';

        $pattern = Kwc_Abstract::getSetting($this->_getParam('componentClass'),
                                                            'stylesIdPattern');
        if ($pattern) {
            //todo: wenns irgendwann berechtigungen gibt hier auch überprüfen ob der aktuelle
            //user diese componentClass & component_id bearbeiten darf
            if (preg_match('#'.$pattern.'#', $this->_getParam('componentId'), $m)) {
                $row->ownStyles = $m[0];
            }
        }
    }
}
