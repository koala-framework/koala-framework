<?php
class Vpc_Form_Dynamic_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Form');
        $ret['componentIcon'] = new Vps_Asset('application_form');
        $ret['generators']['child']['component']['paragraphs'] = 'Vpc_Form_Dynamic_Paragraphs_Component';
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Vps_Form('form');
        $this->_form->setModel(new Vps_Model_FnF()); //TODO
        foreach ($this->getData()->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $this->_form->fields->add($c->getComponent()->getFormField());
        }
    }
}
