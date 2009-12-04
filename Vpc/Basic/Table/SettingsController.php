<?php
class Vpc_Basic_Table_SettingsController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vpc_Basic_Table_Model';
    protected $_formName = 'Vpc_Basic_Table_SettingsForm';
}
