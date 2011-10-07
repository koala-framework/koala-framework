<?php
class Vpc_Guestbook_SettingsForm extends Vps_Form
{
    protected function _initFields()
    {
        $this->setCreateMissingRow(true);
        $this->setLabelWidth(130);

        $this->add(new Vps_Form_Field_ComboBox('new_post_mail', trlVps('Remind mail receiver')))
            ->setValues('/vps/user/changeUser/json-data')
            ->setDisplayField('email')
            ->setWidth(300)
            ->setTriggerAction('all')
            ->setEditable(true)
            ->setForceSelection(true)
            ->setEmptyText('- '.trlVps('Send no mail').' -')
            ->setShowNoSelection(true)
            ->setPageSize(10)
            ->setTpl('<tpl for=".">'.
                        '<div class="x-combo-list-item changeuser-list-item<tpl if="locked != 0"> changeuser-locked</tpl>">'.
                            '<h3><tpl if="lastname">{lastname}&nbsp;</tpl><tpl if="firstname">{firstname}</tpl></h3>'.
                            '{email} <tpl if="role"><span class="changeuser-role">({role})</span></tpl>'.
                        '</div>'.
                      '</tpl>');
        $this->add(new Vps_Form_Field_Select('post_activation_type', trlVps('Post save type')))
            ->setWidth(300)
            ->setValues(array(
                Vpc_Guestbook_Component::ACTIVE_ON_SAVE => trlVps('Active, may be deactivated'),
                Vpc_Guestbook_Component::INACTIVE_ON_SAVE => trlVps('Inactive, must be activated')
            ))
            ->setAllowBlank(false);
    }
}
