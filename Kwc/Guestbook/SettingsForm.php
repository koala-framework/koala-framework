<?php
class Kwc_Guestbook_SettingsForm extends Kwf_Form
{
    protected function _initFields()
    {
        $this->setCreateMissingRow(true);
        $this->setLabelWidth(130);

        $this->add(new Kwf_Form_Field_ComboBox('new_post_mail', trlKwf('Remind mail receiver')))
            ->setValues('/kwf/user/changeUser/json-data')
            ->setDisplayField('email')
            ->setWidth(300)
            ->setTriggerAction('all')
            ->setEditable(true)
            ->setForceSelection(true)
            ->setEmptyText('- '.trlKwf('Send no mail').' -')
            ->setShowNoSelection(true)
            ->setPageSize(10)
            ->setTpl('<tpl for=".">'.
                        '<div class="x2-combo-list-item changeuser-list-item">'.
                            '<h3><tpl if="lastname">{lastname}&nbsp;</tpl><tpl if="firstname">{firstname}</tpl></h3>'.
                            '{email} <tpl if="role"><span class="changeuser-role">({role})</span></tpl>'.
                        '</div>'.
                      '</tpl>');
        $this->add(new Kwf_Form_Field_Select('post_activation_type', trlKwf('Post save type')))
            ->setWidth(300)
            ->setValues(array(
                Kwc_Guestbook_Component::ACTIVE_ON_SAVE => trlKwf('Active, may be deactivated'),
                Kwc_Guestbook_Component::INACTIVE_ON_SAVE => trlKwf('Inactive, must be activated')
            ))
            ->setAllowBlank(false);
    }
}
