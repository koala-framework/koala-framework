<?php
class Vpc_Formular_Dynamic_Component extends Vpc_Formular_Component
{
    private $_settingsModel;
    private $_childComponents;
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['textfield'] = 'Vps_Form_Field_TextField';
        $ret['childComponentClasses']['checkbox'] = 'Vps_Form_Field_Checkbox';
        $ret['childComponentClasses']['select'] = 'Vps_Form_Field_Select';
        $ret['childComponentClasses']['numberfield'] = 'Vps_Form_Field_NumberField';
        $ret['childComponentClasses']['textarea'] = 'Vps_Form_Field_TextArea';
        $ret['childComponentClasses']['fieldset'] = 'Vps_Form_Container_FieldSet';
        $ret['childComponentClasses']['text'] = 'Vpc_Basic_Text_Component';
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
            //todo: ned schön, wegen fieldset (das soll anders, besser speichern)
            "component_id = ? OR component_id LIKE CONCAT(?, '-%')"
                                        => $this->getData()->dbId
        );
        if (!$this->_showInvisible()) {
            $where[] = 'visible = 1';
        }

        $this->_settingsModel = new Vps_Model_Field(array(
            'parentModel' => $t,
            'fieldName' => 'settings'
        ));

        $this->_childComponents = $this->getData()->getChildComponents();

        $fields = array();
        $fields[0] = array();
        foreach ($t->fetchAll($where, 'pos') as $f) {
            if (!$f->parent_id) {
                $fields[0][] = $f;
            } else {
                $fields[$f->parent_id][] = $f;
            }
        }

        $this->_addFields($fields, 0);
    }
    private function _addFields($fields, $parentId)
    {
        //Fieldsets können Unterfelder haben
        //nicht umbedingt optimal gelöst - das hinzufügen gehört mehr
        //zum fieldset finde ich
        foreach ($fields[$parentId] as $field) {
            if ($field->parent_id != $parentId) continue;
            $c = $field->component_class;
            if (is_subclass_of($c, 'Vpc_Abstract')) {
                $f = false;
                foreach ($this->_childComponents as $component) {
                    if ($component->tag == $field->id) {
                        $f = new Vps_Form_Field_ComponentContainer($component->component_id);
                    }
                }
            } else {
                $f = new $c();
                $props = $this->_settingsModel->getRowByParentRow($field)->toArray();
                $f->setProperties($props);
                $f->setName('field'.$field->id);
            }
            if ($f && $field->parent_id) {
                $this->_form->fields['field'.$field->parent_id]->add($f);
            } else if ($f) {
                $this->_form->add($f);
            }

            if (isset($fields[$field->id])) {
                $this->_addFields($fields, $field->id);
            }
        }
    }
}
