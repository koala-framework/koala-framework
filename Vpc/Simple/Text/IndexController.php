<?php
class Vpc_Simple_Text_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array(
            array('type'       => 'TextArea',
                  'fieldLabel' => 'Inhalt',
                  'height'             => 225,
                  'width'		=> 450,
                  'name'       => 'content'));

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Simple_Text_IndexModel';
}
