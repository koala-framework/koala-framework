<?php
class Vpc_Simple_Download_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
	protected $_fields = array(
            array('type'       => 'TextField',
            	 // 'inputType'  => 'file',
                  'fieldLabel' => 'Pfad',
                  'name'       => 'path',
                  'width'      => 200),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Dargestellter Text',
                  'name'       => 'text',
                  'width'      => 200),
            array('type'       => 'TextArea',
                 'fieldLabel' => 'Standardeingabe',
                 'name'       => 'info',
                 'width'      => 200),
                 );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Simple_Download_IndexModel';
    protected $_primaryKey = 'id';
}
