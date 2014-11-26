<?php
class Kwc_Blog_Directory_SettingsForm extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_ComboBox('new_comment_mail_recipient_user_id', trlKwf('New Comment Mail Receiver')))
            ->setValues(Kwc_Admin::getInstance($this->getClass())->getControllerUrl('Users').'/json-data')
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
                            '<h3>{lastname} {firstname}</h3>'.
                            '{email}<tpl if="role"> <span class="changeuser-role">({role})</span></tpl>'.
                        '</div>'.
                      '</tpl>');
    }
}
