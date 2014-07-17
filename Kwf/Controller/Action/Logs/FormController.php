<?php
class Kwf_Controller_Action_Logs_FormController extends Kwf_Controller_Action_Auto_Form
{
    protected $_model = 'Kwf_Log_Model';
    protected $_buttons = array();

    protected function _initFields()
    {
        parent::_initFields();

        $form = $this->_form;

        $form->add(new Kwf_Form_Field_ShowField('date', trlKwf('Time')))
            ->setTpl('{value:localizedDatetime}');
        $form->add(new Kwf_Form_Field_ShowField('type', trlKwf('Type')));
        $form->add(new Kwf_Form_Field_ShowField('exception', trlKwf('Exception')));
        $form->add(new Kwf_Form_Field_ShowField('message', trlKwf('Message')));
        $form->add(new Kwf_Form_Field_ShowField('thrown', trlKwf('Thrown')));
        $form->add(new Kwf_Form_Field_ShowField('exception_detail', trlKwf('Detail')))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('request_uri', trlKwf('Uri')))
            ->setTpl('{value:clickableLink}');
        $form->add(new Kwf_Form_Field_ShowField('useragent', trlKwf('User Agent')));
        $form->add(new Kwf_Form_Field_ShowField('http_referer', trlKwf('Referer')))
            ->setTpl('{value:clickableLink}');
        $form->add(new Kwf_Form_Field_ShowField('user', trlKwf('User')));
        $form->add(new Kwf_Form_Field_ShowField('get', trlKwf('$_GET')))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('post', trlKwf('$_POST')))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('files', trlKwf('$_FILES')))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('session', trlKwf('$_SESSION')))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('server', trlKwf('$_SERVER')))
            ->setTpl('<pre>{value:nl2Br}</pre>');
    }
}

