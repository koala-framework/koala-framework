<?php
class Vps_Form_Cards_TopModel extends Vps_Model_FnF
{
    protected $_rowClass = 'Vps_Form_Cards_TopModelRow';

    protected $_data = array(
        array('id' => 1, 'type' => 'bar', 'comment' => 'bar1'),
        array('id' => 2, 'type' => 'bar', 'comment' => 'bar2'),
        array('id' => 3, 'type' => 'foo', 'comment' => 'foo3'),
        array('id' => 4, 'type' => 'foo', 'comment' => 'foo4')
    );

    public function __construct($config = array())
    {
        $this->_siblingModels = array(
            'foo' => 'Vps_Form_Cards_FooModel',
            'bar' => 'Vps_Form_Cards_BarModel'
        );

        parent::__construct($config);
    }
}