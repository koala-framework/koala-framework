<?php
class Vpc_Formular_Dynamic_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['textfield'] = 'Vps_Form_Field_TextField';
        $ret['childComponentClasses']['checkbox'] = 'Vps_Form_Field_Checkbox';
        $ret['childComponentClasses']['select'] = 'Vps_Form_Field_Select';
        $ret['childComponentClasses']['numberfield'] = 'Vps_Form_Field_NumberField';
        $ret['childComponentClasses']['textarea'] = 'Vps_Form_Field_TextArea';
        $ret['childComponentClasses']['text'] = 'Vpc_Basic_Text_Component';
        $ret['tablename'] = 'Vpc_Formular_Dynamic_Model';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();

        $this->_form = new Vps_Form();
        $this->_form->setModel(new Vps_Model_FnF());

        $t = new Vps_Model_Db(array(
                'table' => new Vpc_Formular_Dynamic_Model()
            ));
        $where = array(
            'component_id = ?' => $this->getTreeCacheRow()->db_id
        );
        if (!$this->_showInvisible()) {
            $where[] = 'visible = 1';
        }

        $settingsModel = new Vps_Model_Field(array(
            'parentModel' => $t,
            'fieldName' => 'settings'
        ));

        $childComponents = $this->getTreeCacheRow()->findChildComponents();

        foreach ($t->fetchAll($where, 'pos') as $field) {
            $c = $field->component_class;
            if (is_subclass_of($c, 'Vpc_Abstract')) {
                $f = false;
                foreach ($childComponents as $component) {
                    if ($component->tag == $field->id) {
                        $f = new Vps_Form_Field_ComponentContainer($component->getComponent());
                    }
                }
            } else {
                $f = new $c();
                $f->setProperties($settingsModel->getRowByParentRow($field)->toArray());
                if (!$f->getName()) $f->setName('field'.$field->id);
            }
            if ($f) $this->_form->add($f);
        }
    }
}
