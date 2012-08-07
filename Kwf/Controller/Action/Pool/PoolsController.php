<?php
/**
 * Der Poolcontroller benötigt Tabelle kwf_pools, die Pools sind im ENUM-Feld
 * 'pool' der Tabelle, ist unter /kwf/pool zu finden, benötigt das Asset
 * KwfPool und muss im jeweiligen Projekt mit der Ressource 'kwf_pool_pools'
 * in der bootstrap hinzugefügt werden.
 */
class Kwf_Controller_Action_Pool_PoolsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_primaryKey = 'pool';
    protected $_buttons = array();

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('pool', 'Pool', 200));
    }

    protected function _fetchData($order, $limit, $start)
    {
        $info = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Pool')->getTable()->info();
        $datatype = $info['metadata']['pool']['DATA_TYPE'];
        $datatype = str_replace(array('enum', '(', "'", ')', ' '), '', $datatype);
        $return = array();
        foreach (explode(',', $datatype) as $t) {
            $return[] = array('pool' => $t);
        }
        return $return;
    }

    public function indexAction()
    {
        $this->view->ext('Kwf.Pool.Panel');
    }
}
