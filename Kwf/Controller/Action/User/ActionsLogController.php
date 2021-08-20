<?php
class Kwf_Controller_Action_User_ActionsLogController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field' => 'date', 'direction' => 'DESC');
    protected $_model = 'Kwf_User_ActionsLogModel';
    protected $_buttons = array();

    public function preDispatch()
    {
        $this->_filters['date'] = array(
            'type' => 'DateRange',
            'label' => trlKwf('Date'),
        );

        $data = array();
        $db = Kwf_Registry::get('db');
        $select = new Zend_Db_Select($db);
        $select->from(
            array('kwf_user_actionslog'),
            array(new Zend_Db_Expr('distinct(user_name)'))
        );
        foreach ($db->fetchAll($select) as $r) {
            $data[] = array($r['user_name'], $r['user_name']);
        }
        $this->_filters['user_name'] = array(
            'type' => 'ComboBox',
            'width' => 100,
            'label' => trlKwf('User'),
            'data' => $data,
        );

        $data = array();
        $db = Kwf_Registry::get('db');
        $select = new Zend_Db_Select($db);
        $select->from(
            array('kwf_user_actionslog'),
            array(new Zend_Db_Expr('distinct(domain)'))
        );
        foreach ($db->fetchAll($select) as $r) {
            $data[] = array($r['domain'], $r['domain']);
        }
        $this->_filters['domain'] = array(
            'type' => 'ComboBox',
            'width' => 100,
            'label' => trlKwf('Domain'),
            'data' => $data,
        );

        $this->_filters['text'] = array(
            'type'=>'TextField',
            'label' => 'Suche',
            'width' => 200
        );
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column('date', trlKwf('Time'), 110))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Kwf_Grid_Column('user_name', trlKwf('User'), 150));
        $this->_columns->add(new Kwf_Grid_Column('domain', trlKwf('Domain'), 120));
        $this->_columns->add(new Kwf_Grid_Column('url', trlKwf('URL'), 300));
        $this->_columns->add(new Kwf_Grid_Column('details', trlKwf('Details'), 150));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        return $ret;
    }
}
