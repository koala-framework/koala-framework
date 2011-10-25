<?php
class Kwc_Guestbook_SettingsController extends Kwf_Controller_Action_Auto_Kwc_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Kwf_Component_FieldModel';
    protected $_formName = 'Kwc_Guestbook_SettingsForm';
}
