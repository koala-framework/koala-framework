<?php

class Vps_Controller_Action_Component_PageEdit extends Vps_Controller_Action_Auto_Form
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Name',
                  'name'       => 'name',
                  'width'      => 230),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Titel',
                  'name'       => 'title',
                  'width'      => 230),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Überschrift',
                  'name'       => 'pagetitle',
                  'width'      => 230)
        );

    protected $_buttons = array('save' => true);
    protected $_tableName = 'Vps_Dao_Pages';
}
