<?php
class Vpc_Forum_User_Edit_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Forum.Edit Forum Account';
        $ret['tablename']     = 'Vpc_Formular_Model';
        $ret['forumUserModel'] = 'Vpc_Forum_User_Model';
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
        $c->store('fieldLabel', trlVps('Forum name'));
        $c->store('isMandatory', true);

        $fieldSettings = array('name'  => 'location',
                               'width' => 250,
                               'value' => ($row ? $row->location : ''));
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'location');
        $c->store('fieldLabel', trlcVps('forum', 'Location', 0));
        $c->store('isMandatory', false);

        $fieldSettings = array('name'  => 'signature',
                               'width' => 250,
                               'height' => 100,
                               'value' => ($row ? $row->signature : ''));
        $c = $this->_createFieldComponent('Textarea', $fieldSettings);
        $c->store('name', 'signature');
        $c->store('fieldLabel',
            trlcVps('forum', 'Signature', 0).'<br />('.
            trlVps('will be displayed at the end of each post').')'
        );
        $c->store('isMandatory', false);

        $fieldSettings = array('name'  => 'description_short',
                               'width' => 250,
                               'height' => 100,
                               'value' => ($row ? $row->description_short : ''));
        $c = $this->_createFieldComponent('Textarea', $fieldSettings);
        $c->store('name', 'description_short');
        $c->store('fieldLabel',
            trlVps('Short description').'<br />('.
            trlVps('interests, hobbies, pets, ...').')'
        );
        $c->store('isMandatory', false);

        $fieldSettings = array(
            'value' => '<br />'
                .trlVps('You may upload a picture that is displayed in your profile
                and in each of your posts in a small version (40x40 Pixels).')
                .'<br />'
                .trlVps('For optimized displayment the picture should have a width of at least 150 Pixels,
                the file size may not be bigger than 2 MB.'),
            'name'  => 'avatarinfo'
        );
        $c = $this->_createFieldComponent('ShowText', $fieldSettings);
        $c->store('name', 'avatarinfo');
//         $c->store('fieldLabel', trlVps('Information'));
        $c->store('isMandatory', false);

        $fieldSettings = array('name'  => 'avatar',
                               'width' => 250);
        $c = $this->_createFieldComponent('FileUpload', $fieldSettings);
        $c->store('name', 'avatar');
        $c->store('fieldLabel', trlVps('Profile picture'));
        $c->store('isMandatory', false);

        if ($row && $row->avatar) {
            $fieldSettings = array('name'  => 'avatar_delete',
                                   'width' => 250,
                                   'value' => 1,
                                   'checked' => false,
                                   'text' => trlVps('Delete current picture')
                                        .'<br /><img src="'.$row->getFileUrl('Avatar', 'avatar').'" class="avatar" alt="Avatar" />');
            $c = $this->_createFieldComponent('Checkbox', $fieldSettings);
            $c->store('name', 'avatar_delete');
            $c->store('fieldLabel', trlVps('Delete picture'));
            $c->store('isMandatory', false);
        }

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => trlVps('Save properties')
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
                if (!in_array($name, array('sbmt', 'avatar_delete', 'avatarinfo'))) {
                    if ($name != 'avatar' || $c->getValue() != null) {
                        if ($name == 'avatar' && $c->getValue() != null && $row->avatar) {
                            $ft = new Vps_Dao_File();
                            $ft->fetchRow(array('id = ?' => $row->avatar))->delete();
                        }
                        $row->$name = $c->getValue();
                    }
                } else if ($name == 'avatar_delete') {
                    if ($c->getSent()) {
                        $ft = new Vps_Dao_File();
                        $ft->fetchRow(array('id = ?' => $row->avatar))->delete();
                        $row->avatar = null;
                    }
                }
            }
        }
        $row->save();
    }
}
