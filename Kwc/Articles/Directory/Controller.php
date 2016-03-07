<?php
class Kwc_Articles_Directory_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_paging = 25;
    protected $_filters = array('text'=>true);
    protected $_defaultOrder = array(
        array('field'=>'date', 'direction'=>'DESC'),
        array('field'=>'priority', 'direction'=>'DESC')
    );

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwc.articles.directory.tabs';
    }

    protected function _initColumns()
    {
        $this->_filters['deleted'] = array(
            'type'=>'Button',
            'icon'=>'/assets/silkicons/bin.png',
            'cls'=>'x2-btn-icon'
        );
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 200));
        $this->_columns->add(new Kwf_Grid_Column_Date('date', trlKwf('Publication')));
        $this->_columns->add(new Kwf_Grid_Column_Visible('visible'));
        $this->_columns->add(new Kwf_Grid_Column('vi_nr', trlKwf('VI-Nr'), 50));
        $this->_columns->add(new Kwf_Grid_Column('is_top', '&nbsp', 25))
            ->setRenderer('booleanIcon')
            ->setIcon('/assets/silkicons/exclamation.png')
            ->setTooltip('Top-Thema');
        $this->_columns->add(new Kwf_Grid_Column('read_required', '&nbsp', 25))
            ->setRenderer('booleanIcon')
            ->setIcon('/assets/silkicons/stop.png')
            ->setTooltip('Lesepflichtig');
        $this->_columns->add(new Kwf_Grid_Column('only_intern', '&nbsp', 25))
            ->setRenderer('booleanIcon')
            ->setIcon('/assets/silkicons/eye.png')
            ->setTooltip('Nur Intern');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('deleted', 0);
        if ($this->_getParam('query_deleted')) {
            $ret->whereEquals('deleted', $this->_getParam('query_deleted'));
        }
        return $ret;
    }

    protected function _deleteRow(Kwf_Model_Row_Interface $row)
    {
        $row->deleted = 1;
        $row->save();
    }

    public function jsonRestoreAction()
    {
        if (!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Kwf_Exception("Restore is not allowed.");
        }
        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);

        ignore_user_abort(true);
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->beginTransaction();
        foreach ($ids as $id) {
            $row = $this->_model->getRow($id);
            if (!$row) {
                throw new Kwf_Exception_Client("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'delete')) {
                throw new Kwf_Exception("You don't have the permissions to delete this row.");
            }
            $row->deleted = 0;
            $row->save();
        }
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->commit();
    }
}
