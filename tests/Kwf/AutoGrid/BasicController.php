<?php
class Vps_AutoGrid_BasicController extends Vps_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = 'id';

    public function indexAction()
    {
        $this->view->assetsType = 'Vps_AutoGrid:Test';
        $this->view->viewport = 'Vps.Test.Viewport';
        parent::indexAction();
    }

    public function preDispatch()
    {
        $this->_filters = array(
            'text' => true,
            'type' => array(
                'type' => 'ComboBox',
                'data' => array(array('X', 'X'), array('Y', 'Y'), array('Z', 'Z'))
            )
        );
        $this->_model = new Vps_Model_FnF();
        $this->_model->setData(array(
            array('id' => 1, 'value' => 'Herbert', 'value2' => 'Lorenz', 'testtime' => '2008-12-03', 'type' => 'X'),
            array('id' => 2, 'value' => 'Kurt', 'value2' => 'Herbert', 'testtime' => '2008-12-06', 'type' => 'X'),
            array('id' => 3, 'value' => 'Klaus', 'value2' => 'Kurt', 'testtime' => '2008-12-09', 'type' => 'Y'),
            array('id' => 4, 'value' => 'Rainer', 'value2' => 'Klaus', 'testtime' => '2008-12-12', 'type' => 'Y'),
            array('id' => 5, 'value' => 'Franz', 'value2' => 'Rainer', 'testtime' => '2008-12-10', 'type' => 'Z'),
            array('id' => 6, 'value' => 'Niko', 'value2' => 'Franz', 'testtime' => '2008-12-15', 'type' => 'Z'),
            array('id' => 7, 'value' => 'Lorenz', 'value2' => 'Niko', 'testtime' => '2008-12-18', 'type' => 'Z'),
        ));
        $this->_model->setColumns(array('id', 'value', 'value2', 'testtime', 'type'));
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        $this->_filters['value'] = array(
            'type'      => 'Button',
            'skipWhere' => true,
            'text' => 'Filter'
        );

        $this->_columns->add(new Vps_Grid_Column('id', 'Id', 50));
        $this->_columns->add(new Vps_Grid_Column('value', 'Context', 100));
        $this->_columns->add(new Vps_Grid_Column('value2', 'Context2', 100));
        $this->_columns->add(new Vps_Grid_Column('type', 'Type', 50));
        parent::_initColumns();
    }

    public function fetchData($order, $limit, $start)
    {
        return $this->_fetchData($order, $limit, $start);
    }
}