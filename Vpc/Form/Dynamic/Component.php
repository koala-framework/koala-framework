<?php
class Vpc_Form_Dynamic_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Form');
        $ret['componentIcon'] = new Vps_Asset('application_form');
        $ret['generators']['child']['component']['paragraphs'] = 'Vpc_Form_Dynamic_Paragraphs_Component';
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Vps_Form('form');
        foreach ($this->getData()->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $this->_form->fields->add($c->getComponent()->getFormField());
        }
        $this->_form->setModel(new Vps_Model_Mail(array('componentClass' => get_class($this))));
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Vps_Registry::get('config')->server->domain;
        }
        $row->setFrom("noreply@$host");
        $row->addTo($this->getRow()->recipient);
        $row->subject = $this->getRow()->subject;

        $labels = array();
        foreach ($this->getData()->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $f = $c->getComponent()->getFormField();
            if ($f->getName() && $f->getFieldLabel()) {
                $labels[$f->getName()] = $f->getFieldLabel();
            }
        }
        $row->field_labels = serialize($labels); //ouch TODO bessere loesung
    }
}
