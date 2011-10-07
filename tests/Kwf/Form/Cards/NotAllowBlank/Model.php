<?php
class Kwf_Form_Cards_NotAllowBlank_Model extends Kwf_Model_Session
{
    protected $_namespace = 'Kwf_Form_Cards_NotAllowBlank_Model';
    protected $_columns = array('id', 'type', 'comment');

    protected $_dependentModels = array(
        'ToRelation' => 'Kwf_Form_Cards_NotAllowBlank_RelationModel'
    );

    protected $_defaultData = array(
        array('id' => 1, 'type' => 'bar', 'comment' => 'bar1'),
        array('id' => 2, 'type' => 'bar', 'comment' => 'bar2'),
        array('id' => 3, 'type' => 'foo', 'comment' => 'foo3'),
        array('id' => 4, 'type' => 'foo', 'comment' => 'foo4')
    );
}