<?php

class Vps_Controller_Action_Component_PageEditController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);
    protected $_tableName = 'Vps_Dao_Pages';

    public function _isAllowed($user)
    {
        if ($this->_getParam('id')) {
            $id = $this->_getParam('id');
        } else {
            $id = $this->_getParam('parent_id');
        }
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($id, array('ignoreVisible'=>true));
        if (!$c) {
            throw new Vps_Exception("Can't find component to check permissions");
        }
        return Vps_Registry::get('acl')->getComponentAcl()
            ->isAllowed($this->_getAuthData(), $c);
        return true;
    }

    protected function _initFields()
    {
        $model = new Vps_Component_ComponentModel();
        $data = array();
        foreach ($model->getRows() as $d) {
            $data[] = array($d->id, $d->name, $d->domain);
        }

        $fields = $this->_form->fields;
        $fields->add(new Vps_Form_Field_TextField('name', trlVps('Name of Page')))
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Select('component',  trlVps('Pagetype')))
            ->setStore(array('data' => $data, 'fields' => array('id', 'name', 'domain')))
            ->setTpl('<tpl for="."><div class="x-combo-list-item">{name}</div></tpl>')
            ->setAllowBlank(false);
        $fields->add(new Vps_Form_Field_Checkbox('hide',  trlVps('Hide in Menu')));
        $fields->add(new Vps_Form_Field_TextField('tags', trlVps('Tags')));
        $fields->add(new Vps_Form_Field_LoadData('domain'));
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        if (is_numeric($this->_getParam('parent_id'))) {
            $row->parent_id = $this->_getParam('parent_id');
        } else {
            preg_match('#^root-([^-]+)-?([^-]*)$#', $this->_getParam('parent_id'), $m);
            $row->parent_id = null;
            if ($m[2]) {
                $row->domain = $m[1];
                $row->category = $m[2];
            } else {
                $row->domain = null;
                $row->category = $m[1];
            }
        }
    }
}
