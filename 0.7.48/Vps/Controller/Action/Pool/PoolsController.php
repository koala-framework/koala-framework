<?php
/**
 * Der Poolcontroller benötigt Tabelle vps_pools, die Pools sind im ENUM-Feld
 * 'pool' der Tabelle, ist unter /vps/pool zu finden, benötigt das Asset
 * VpsPool und muss im jeweiligen Projekt mit der Ressource 'vps_pool_pools'
 * in der bootstrap hinzugefügt werden.
 */
class Vps_Controller_Action_Pool_PoolsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_primaryKey = 'pool';
    protected $_buttons = array();

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('pool', 'Pool', 200));
    }

    protected function _fetchData($order, $limit, $start)
    {
        $table = new Vps_Dao_Pool();
        $info = $table->info();
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
        $this->view->ext('Vps.Pool.Panel');
    }
}
