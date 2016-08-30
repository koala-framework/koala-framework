<?php
class Kwf_Controller_Action_Redirects_RedirectsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Kwf_Util_Model_Redirects';

    protected $_defaultOrder = 'source';
    protected $_paging = 25;
    protected $_filters = array('text'=>array('type'=>'Text', 'width'=>100));

    public function indexAction()
    {
        $this->view->ext('Kwf.Redirects.Index');
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column_Visible('active'));
        $domains = Kwf_Controller_Action_Redirects_RedirectController::getDomains();
        if ($domains && count($domains) > 1) {
            $this->_columns->add(new Kwf_Grid_Column('domain_component_id', trlKwf('Domain'), 70));
            $values = array();
            foreach ($domains as $k=>$i) {
                $values[] = array($k, $i);
            }
            $this->_filters['domain_component_id'] = array(
                'type' => 'ComboBox',
                'data' => $values,
                'width' => 100
            );
        }
        $this->_columns->add(new Kwf_Grid_Column('source', trlKwf('Source'), 200));
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment'), 150));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $domains = Kwf_Controller_Action_Redirects_RedirectController::getDomains();
        if ($domains) {
            if (!$this->_getParam('query_domain_component_id')) {
                $ret->whereEquals('domain_component_id', array_keys($domains));
            }
        }
        return $ret;
    }
}
