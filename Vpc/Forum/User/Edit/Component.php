<?php
class Vpc_Forum_User_Edit_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Forum.Edit Forum Account';
        $ret['tablename']     = 'Vpc_Formular_Model';
        $ret['forumUserModel'] = 'Vpc_Forum_User_Model';
        $ret['hideInNews']    = true;
        $ret['childComponentClasses']['success'] = 'Vpc_Forum_User_Edit_Success_Component';
        return $ret;
    }

    protected function _init()
    {
        parent::_init();

        $row = $this->_getEditRow();
/*
        $fieldSettings = array('name'  => 'avatar',
                               'width' => 250);
        $c = $this->_createFieldComponent('FileUpload', $fieldSettings);
        $c->store('name', 'avatar');
        $c->store('fieldLabel', 'Avatar');
        $c->store('isMandatory', false);
*/
        $fieldSettings = array('name'  => 'nickname',
                               'width' => 250,
                               'value' => ($row ? $row->nickname : ''));
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'nickname');
        $c->store('fieldLabel', 'Name für Forum');
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'location',
                               'width' => 250,
                               'value' => ($row ? $row->location : ''));
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'location');
        $c->store('fieldLabel', 'Ort');
        $c->store('isMandatory', false);

        $fieldSettings = array('name'  => 'signature',
                               'width' => 250,
                               'height' => 100,
                               'value' => ($row ? $row->signature : ''));
        $c = $this->_createFieldComponent('Textarea', $fieldSettings);
        $c->store('name', 'signature');
        $c->store('fieldLabel', 'Signatur');
        $c->store('isMandatory', false);

        $fieldSettings = array('name'  => 'description_short',
                               'width' => 250,
                               'height' => 100,
                               'value' => ($row ? $row->description_short : ''));
        $c = $this->_createFieldComponent('Textarea', $fieldSettings);
        $c->store('name', 'description_short');
        $c->store('fieldLabel', 'Kurze Beschreibung');
        $c->store('isMandatory', false);

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => trlVps('Change properties')
        ));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['formTemplate'] = Vpc_Admin::getComponentFile('Vpc_Formular_Component', '', 'tpl');
        return $ret;
    }

    protected function _getEditRow()
    {
        $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
        if (!$authedUser) return ;

        $tableName = $this->_getSetting('forumUserModel');
        $table = new $tableName();
        $row = $table->find($authedUser->id)->current();
        return $row;
    }

    protected function _processForm()
    {
        $row = $this->_getEditRow();
        if (!$row) return ;

        $forumUserTable = new Vpc_Forum_User_Model();
        $nickExists = $forumUserTable->fetchRow(array(
            'id != ?' => $row->id,
            'nickname = ?' => $_POST['nickname']
        ));
        if ($nickExists) {
            throw new Vps_ClientException(trlVps('This "name for the forum" already exists. ')
                .trlVps('Please choose a different.'));
        }

        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                if (!in_array($name, array('sbmt'))) {
                    $row->$name = $c->getValue();
                }
            }
        }
        $row->save();
    }
}
