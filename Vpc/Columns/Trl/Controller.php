<?php
class Vpc_Columns_Trl_Controller extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();

    public function indexAction()
    {
        //nicht: parent::indexAction();
        $this->view->xtype = 'vps.component';
        $this->view->mainComponentClass = $this->_getParam('class');
        $this->view->baseParams = array('id' => $this->_getParam('componentId'));

        $this->view->componentConfigs = array();
        $this->view->mainEditComponents = array();
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        if (!$config) {
            throw new Vps_Exception("Not ExtConfig avaliable for this component");
        }
        foreach ($config as $k=>$c) {
            $this->view->componentConfigs[$this->_getParam('class').'-'.$k] = $c;
            $this->view->mainEditComponents[] = array(
                'componentClass' => $this->_getParam('class'),
                'type' => $k
            );
        }
        $this->view->mainType = $this->view->mainEditComponents[0]['type'];
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('id'));
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Column'), 200));
        $this->_columns->add(new Vps_Grid_Column_Button('edit'));
    }

    
    protected function _fetchData($order, $limit, $start)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($this->_getParam('componentId'), array('ignoreVisible'=>true));
        $cols = $c->chained->getChildComponents(array('generator'=>'columns'));
        $ret = array();
        $i = 0;
        foreach ($cols as $col) {
            $i++;
            $ret[] = array(
                'id' => $col->id,
                'name' => trlVps('Column {0}', $i)
            );
        }
        return $ret;
    }
}
