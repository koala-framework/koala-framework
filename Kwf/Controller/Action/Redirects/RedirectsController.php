<?php
class Kwf_Controller_Action_Redirects_RedirectsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Kwf_Util_Model_Redirects';

    public function indexAction()
    {
        $this->view->ext('Kwf.Redirects.Index');
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('source', trlKwf('Source')));
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')));
        $this->_columns->add(new Kwf_Grid_Column_Visible('active'));
    }
}
