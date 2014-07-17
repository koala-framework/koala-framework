<?php
class Kwf_Controller_Action_Log_GridController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('delete');
    protected $_model = 'Kwf_Log_Model';
    protected $_paging = 25;
    protected $_defaultOrder = array(
        'direction' => 'DESC',
        'field' => 'date'
    );

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwf.logs.panel';
        $this->view->controllerUrl = '/kwf/component/logs';
        $this->view->formControllerUrl = '/kwf/component/logs-form';
    }

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_filters['type'] = array(
            'type'   => 'ComboBox',
            'text'   => trlKwf('Type'),
            'data'   => array(
                array('error', trlKwf('Error')),
                array('accessdenied', trlKwf('Access Denied')),
                array('notfound', trlKwf('Not Found')),
            ),
            'width'  => 100
        );

        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width' => 300
        );

        $columns = $this->_columns;
        $columns->add(new Kwf_Grid_Column_Datetime('date'));
        $columns->add(new Kwf_Grid_Column('type', trlKwf('Type'), 80));
        $columns->add(new Kwf_Grid_Column('message', trlKwf('Message'), 300));
        $columns->add(new Kwf_Grid_Column('request_uri', trlKwf('Uri'), 200))
            ->setRenderer('clickableLink');
        $columns->add(new Kwf_Grid_Column('http_referer', trlKwf('Referer'), 200))
            ->setRenderer('clickableLink');
        $columns->add(new Kwf_Grid_Column('user', trlKwf('User'), 200));
    }


}

