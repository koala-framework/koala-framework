<?php
class Kwf_Controller_Action_User_ActionsLogController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field' => 'date', 'direction' => 'DESC');
    protected $_model = 'Kwf_User_ActionsLogModel';
    protected $_buttons = array();
    protected $_paging = 200;

    public function preDispatch()
    {
        $this->_filters['date'] = array(
            'type' => 'DateRange',
            'label' => trlKwf('Date'),
        );

        $data = array();
        $db = Kwf_Registry::get('db');
        $select = new Zend_Db_Select($db);
        $select = $this->_createSelectToShowOnlyPermittedEntriesZendExpression($select);
        $currentUser = Kwf_Registry::get('userModel')->getAuthedUser();
        $allowedDomains = $this->_getAllowedDomains($currentUser);
        if (!empty($allowedDomains)) {
            $select->from(
                array('kwf_user_actionslog'),
                array(new Zend_Db_Expr('distinct(user_name)'))
            );
            foreach ($db->fetchAll($select) as $r) {
                $data[] = array($r['user_name'], $r['user_name']);
            }
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
        $select = $this->_createSelectToShowOnlyPermittedEntriesZendExpression($select);
        if (!empty($allowedDomains)) {
            $select->from(
                array('kwf_user_actionslog'),
                array(new Zend_Db_Expr('distinct(domain)'))
            );
            foreach ($db->fetchAll($select) as $r) {
                $data[] = array($r['domain'], $r['domain']);
            }
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
        $currentUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($this->_getAllowedDomains($currentUser)) {
            $ret = $this->_createSelectToShowOnlyPermittedEntriesKwfExpression($ret);
        } else {
            // Return no result (id is Auto_Increment and can't be -1)
            $ret = $ret->where(new Kwf_Model_Select_Expr_Equal('id', '-1'));
        }
        return $ret;
    }

    private function _createSelectToShowOnlyPermittedEntriesZendExpression($select) {
        $currentUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($currentUser->role == 'admin') return $select;

        foreach ($this->_getAllowedDomains($currentUser) as $domainName) {
            $select->orWhere('domain = ?', $domainName);
        }

        return $select;
    }

    private function _createSelectToShowOnlyPermittedEntriesKwfExpression($select) {
        $currentUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($currentUser->role == 'admin') return $select;
        $orExpr = new Kwf_Model_Select_Expr_Or(array());

        foreach ($this->_getAllowedDomains($currentUser) as $domainName) {
            $orExpr->addExpression(new Kwf_Model_Select_Expr_Equal('domain', $domainName));
        }

        return $select->where($orExpr);
    }

    private function _getAllowedDomains($currentUser) {
        $domainNames = array();
        foreach ($currentUser->getDomains() as $domain => $value ) {
            $domainComponent = Kwf_Component_Data_Root::getInstance()->getComponentByDbId('root-'.$domain);
            $domainNames[] = $domainComponent ? $domainComponent->getRow()->name : null;
        }
        return $domainNames;
    }
}
