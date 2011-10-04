<?php
class Vps_Controller_Action_Redirects_RedirectsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Vps_Util_Model_Redirects';

    public function indexAction()
    {
        $this->view->ext('Vps.Redirects.Index');
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('source', trlVps('Source')));
        $this->_columns->add(new Vps_Grid_Column('comment', trlVps('Comment')));
        $this->_columns->add(new Vps_Grid_Column_Visible('active'));
    }
}
