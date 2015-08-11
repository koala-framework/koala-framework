<?php
class Kwf_Controller_Action_Debug_LogsFormController extends Kwf_Controller_Action_Auto_Form
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
        $form->add(new Kwf_Form_Field_ShowField('exception', 'Exception'));
        $form->add(new Kwf_Form_Field_ShowField('message', trlKwf('Message')));
        $form->add(new Kwf_Form_Field_ShowField('thrown', 'Thrown'));
        $form->add(new Kwf_Form_Field_ShowField('exception_detail', trlKwf('Detail')))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('request_uri', 'Uri'))
            ->setTpl('{value:clickableLink}');
        $form->add(new Kwf_Form_Field_ShowField('useragent', 'User Agent'));
        $form->add(new Kwf_Form_Field_ShowField('http_referer', 'Referer'))
            ->setTpl('{value:clickableLink}');
        $form->add(new Kwf_Form_Field_ShowField('user', trlKwf('User')));
        $form->add(new Kwf_Form_Field_ShowField('get', '$_GET'))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('post', '$_POST'))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('files', '$_FILES'))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('session', '$_SESSION'))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('server', '$_SERVER'))
            ->setTpl('<pre>{value:nl2Br}</pre>');
        $form->add(new Kwf_Form_Field_ShowField('raw_body', 'Raw Body'))
            ->setTpl('<pre>{value:nl2Br}</pre>');
    }
}

