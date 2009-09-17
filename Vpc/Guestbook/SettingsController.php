<?php
class Vpc_Guestbook_SettingsController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vps_Component_FieldModel';
    protected $_formName = 'Vpc_Guestbook_SettingsForm';
}
