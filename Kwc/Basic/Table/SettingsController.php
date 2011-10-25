<?php
class Kwc_Basic_Table_SettingsController extends Kwf_Controller_Action_Auto_Kwc_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Kwc_Basic_Table_Model';
    protected $_formName = 'Kwc_Basic_Table_SettingsForm';
}
