<?php
class Vpc_Guestbook_SettingsController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vps_Component_FieldModel';

    public function _initFields()
    {
        $this->_form->setId($this->_getParam('componentId'));
        $this->_form->setLabelWidth(130);
        $this->_form->add(new Vps_Form_Field_ComboBox('new_post_mail', trlVps('Remind mail receiver')))
            ->setValues('/vps/user/changeUser/json-data')
            ->setDisplayField('email')
            ->setWidth(300)
            ->setTriggerAction('all')
            ->setEditable(true)
            ->setForceSelection(true)
            ->setEmptyText('- '.trlVps('Send no mail').' -')
            ->setPageSize(10)
            ->setTpl('<tpl for=".">'.
                        '<div class="x-combo-list-item changeuser-list-item<tpl if="locked != 0"> changeuser-locked</tpl>">'.
                            '<h3>{lastname}&nbsp;{firstname}</h3>'.
                            '{email} <span class="changeuser-role">({role})</span>'.
                        '</div>'.
                      '</tpl>');
        $this->_form->add(new Vps_Form_Field_Select('post_activation_type', trlVps('Post save type')))
            ->setWidth(300)
            ->setValues(array(
                Vpc_Guestbook_Component::ACTIVE_ON_SAVE => trlVps('Active, may be deactivated'),
                Vpc_Guestbook_Component::INACTIVE_ON_SAVE => trlVps('Inactive, must be activated')
            ))
            ->setAllowBlank(false);
    }
}
