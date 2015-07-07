<?php
class Kwc_Columns_Abstract_SettingsController extends Kwf_Controller_Action_Auto_Kwc_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_model = 'Kwf_Component_Model';
    protected $_formName = 'Kwc_Columns_Abstract_SettingsForm';
}
