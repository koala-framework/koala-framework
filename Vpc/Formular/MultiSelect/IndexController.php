<?php
class Vpc_Formular_MultiSelect_IndexController extends Vpc_Formular_Select_IndexController
{
    protected $_fields = array(
            array('type'       => 'ComboBox',
                  'fieldLabel' => 'Typ',
                  'hiddenName' => 'type',
                  'mode'       => 'local',
                  'store'      => array('data' => array(array('checkbox', 'Checkboxen'),
                                                        array('checkbox_horizontal', 'Checkboxen horizontal'),
                                                        array('select', 'Select-Feld')),
                                       ),
                  'editable'   => false,
                  'triggerAction'=>'all'),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Größe des Select-Felds',
                  'name'       => 'size',
                  'width'      => 60)
    );
    protected $_tableName = 'Vpc_Formular_MultiSelect_IndexModel';
}