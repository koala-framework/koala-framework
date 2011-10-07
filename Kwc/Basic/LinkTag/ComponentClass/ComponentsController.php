<?php
class Kwc_Basic_LinkTag_ComponentClass_ComponentsController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('id'));
        $this->_columns->add(new Kwf_Grid_Column('title'));
    }

    protected function _fetchData($order, $limit, $start)
    {
        $ret = array();
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass(Kwc_Abstract::getSetting($this->_getParam('class'), 'targetComponentClass'));
        $dbIds = array();
        foreach ($components as $c) {
            if (!in_array($c->dbId, $dbIds)) {
                $dbIds[] = $c->dbId;
                $ret[] = array(
                    'id' => $c->dbId,
                    'title' => $c->getTitle()
                );
            }
        }
        return $ret;
    }
}
