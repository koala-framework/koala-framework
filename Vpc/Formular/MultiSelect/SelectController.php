<?php
class Vpc_Formular_MultiSelect_OptionsController extends Vps_Controller_Action_Auto_Grid_Vpc
{
    protected $_columns = array(
                        array('dataIndex' => 'text',
                              'header'    => 'Bezeichnung',
                              'width'     => 200,
                              'editor'    => array('type' => 'TextField',
                              'allowBlank' => true)),
                        array('dataIndex' => 'checked',
                              'header'    => 'Angehakt',
                              'width'     => 60,
                              'editor'    => 'Checkbox'));

    protected $_tableName = 'Vpc_Formular_MultiSelect_OptionsModel';
    protected $_position = 'pos';
}