<?php
class Vps_AutoGrid_BasicController extends Vps_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = 'id';

    public function preDispatch()
    {
        $this->_model = new Vps_Model_Fnf();
        $this->_model->setData(array(
            array('id' => 1, 'value' => 'Herbert', 'testtime' => '2008-12-03'),
            array('id' => 2, 'value' => 'Kurt', 'testtime' => '2008-12-06'),
            array('id' => 3, 'value' => 'Klaus', 'testtime' => '2008-12-09'),
            array('id' => 4, 'value' => 'Rainer', 'testtime' => '2008-12-12'),
            array('id' => 5, 'value' => 'Franz', 'testtime' => '2008-12-10'),
            array('id' => 6, 'value' => 'Niko', 'testtime' => '2008-12-15'),
            array('id' => 7, 'value' => 'Lorenz', 'testtime' => '2008-12-18'),
        ));
        $this->_model->setColumns(array('id', 'value', 'testtime'));
        parent::preDispatch();
    }

    protected function _initColumns()
    {

        $this->_columns->add(new Vps_Grid_Column('id', 'Id', 50));
        $this->_columns->add(new Vps_Grid_Column('value', 'Context', 100));
        parent::_initColumns();


    }

    public function fetchData($order, $limit, $start)
    {
        return $this->_fetchData($order, $limit, $start);
    }
}