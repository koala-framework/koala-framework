<?php
class Vpc_Formular_Dynamic_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['textfield'] = 'Vps_Form_Field_TextField';
        $ret['childComponentClasses']['checkbox'] = 'Vps_Form_Field_Checkbox';
        $ret['childComponentClasses']['select'] = 'Vps_Form_Field_Select';
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

        foreach ($t->fetchAll($where, 'pos') as $field) {
            $c = $field->component_class;
            $f = new $c();
            $f->setProperties($settingsModel->getRowByParentRow($field)->toArray());
            if (!$f->getName()) $f->setName('field'.$field->id);
            $this->_form->add($f);
        }
    }
}
