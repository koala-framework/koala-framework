<?php
abstract class Kwf_Controller_Action_Auto_ImageGrid extends Kwf_Controller_Action_Auto_Abstract
{
    // the rule to the image. defaults to the first rule with model Kwf_Uploads_Model
    protected $_imageRule = null;
    protected $_labelField; // default is __toString()
    protected $_filters = null;

    protected $_model;
    protected $_additionalDataFields = array();

    protected $_primaryKey;
    protected $_paging = 0;

    protected $_maxLabelLength = 16;

    protected $_textField = 'name';
    protected $_buttons = array(
        'add'       => true,
        'edit'      => false,
        'delete'    => true,
        'invisible' => null,
        'reload'    => true
    );
    protected $_editDialog;
    protected $_defaultOrder;

    public function indexAction()
    {
        $this->view->controllerUrl = $this->getRequest()->getBaseUrl().$this->getRequest()->getPathInfo();
        $this->view->xtype = 'kwf.imagegrid';
    }

    public function init()
    {
        parent::init();
        $this->_filters = new Kwf_Controller_Action_Auto_FilterCollection();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (is_string($this->_model)) {
            $this->_model = new $this->_model();
        }

        // PrimaryKey setzen
        if (!isset($this->_primaryKey)) {
            $this->_primaryKey = $this->_model->getPrimaryKey();
        }

        if (is_string($this->_defaultOrder)) {
            $this->_defaultOrder = array(
                'field' => $this->_defaultOrder,
                'direction'   => 'ASC'
            );
        }
        if (!$this->_imageRule) {
            $this->_imageRule = $this->_model->getReferenceRuleByModelClass('Kwf_Uploads_Model');
        }
    }

    private function _getImageReference()
    {
        return $this->_model->getReference($this->_imageRule);
    }

    public function jsonDataAction()
    {
        $limit = null; $start = null; $order = 0;
        if ($this->_paging) {
            $limit = $this->getRequest()->getParam('limit');
            $start = $this->getRequest()->getParam('start');
            if (!$limit) {
                if (!is_array($this->_paging) && $this->_paging > 0) {
                    $limit = $this->_paging;
                } else if (is_array($this->_paging) && isset($this->_paging['pageSize'])) {
                    $limit = $this->_paging['pageSize'];
                } else {
                    $limit = $this->_paging;
                }
            }
        }
        $order = $this->_defaultOrder;

        $rowSet = $this->_fetchData($order, $limit, $start);
        if (!is_null($rowSet)) {
            $rows = array();
            foreach ($rowSet as $row) {
                if (is_array($row)) {
                    $row = (object)$row;
                }
                if (!$this->_hasPermissions($row, 'load')) {
                    throw new Kwf_Exception("You don't have the permissions to load this row");
                }
                $rows[] = $this->_createItemByRow($row);
            }

            $this->view->rows = $rows;
            if ($this->_paging) {
                $this->view->total = $this->_fetchCount();
            } else {
                $this->view->total = sizeof($rows);
            }
        } else {
            $this->view->total = 0;
            $this->view->rows = array();
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }
    }

    protected function _createItemByRow($row)
    {
        $truncateHelper = new Kwf_View_Helper_Truncate();
        $primaryKey = $this->_primaryKey;
        $r = array();

        if (!isset($r[$primaryKey]) && isset($row->$primaryKey)) {
            $r[$primaryKey] = $row->$primaryKey;
        }

        if (!$this->_labelField && $row instanceof Kwf_Model_Row_Interface) {
            $r['label'] = $row->__toString();
        } else if ($this->_labelField) {
            $r['label'] = $row->{$this->_labelField};
        } else {
            throw new Kwf_Exception("You have to set _labelField in the ImageGrid Controller");
        }

        if (!empty($r['label'])) $r['label_short'] = $r['label'];

        if (!empty($r['label_short']) && $this->_maxLabelLength) {
            $r['label_short'] = $truncateHelper->truncate($r['label_short'], $this->_maxLabelLength, '…', true, false);
        }

        $imageRef = $this->_getImageReference();
        $hashKey = Kwf_Util_Hash::hash($row->{$imageRef['column']});
        $r['src'] = '/kwf/media/upload/preview?uploadId='.$row->{$imageRef['column']}.
            '&hashKey='.$hashKey.'&size=imageGrid';
        $r['src_large'] = '/kwf/media/upload/preview?uploadId='.$row->{$imageRef['column']}.
            '&hashKey='.$hashKey.'&size=imageGridLarge';
        if ($uploadRow = $row->getParentRow($this->_imageRule)) {
            $dim = Kwf_Media_Image::calculateScaleDimensions($uploadRow->getFileSource(), array(400, 400, Kwf_Media_Image::SCALE_BESTFIT));
            $r['src_large_width'] = $dim['width'];
            $r['src_large_height'] = $dim['height'];
        }

        foreach($this->_additionalDataFields as $f) {
            $r[$f] = $row->{$f};
        }

        return $r;
    }

    protected function _appendMetaData()
    {
        $this->view->metaData = array();

        $this->view->metaData['root'] = 'rows';
        $this->view->metaData['id'] = $this->_primaryKey;
        $this->view->metaData['fields'] = array('id', 'label', 'label_short', 'src', 'src_large', 'src_large_width', 'src_large_height');
        foreach($this->_additionalDataFields as $f) {
            $this->view->metaData['fields'][] = $f;
        }
        $this->view->metaData['totalProperty'] = 'total';
        $this->view->metaData['successProperty'] = 'success';
        $this->view->metaData['buttons'] = (object)$this->_buttons;
        $this->view->metaData['permissions'] = (object)$this->_permissions;
        $this->view->metaData['paging'] = $this->_paging;
        $this->view->metaData['editDialog'] = $this->_editDialog;

        $filters = array();
        foreach ($this->_filters as $filter) {
            $filters[] = $filter->getExtConfig();
        }
        $this->view->metaData['filters'] = $filters;
    }

    protected function _hasPermissions($row, $action)
    {
        return true;
    }

    protected function _getSelect()
    {
        $ret = $this->_model->select();

        // Filter
        foreach ($this->_filters as $filter) {
            if ($filter->getSkipWhere()) continue;
            $ret = $filter->formatSelect($ret, $this->_getAllParams());
        }

        $queryId = $this->getRequest()->getParam('queryId');
        if ($queryId) {
            $ret->where(new Kwf_Model_Select_Expr_Equal($this->_primaryKey, $queryId));
        }

        return $ret;
    }

    protected function _fetchData($order, $limit, $start)
    {
        if (!isset($this->_model)) {
            throw new Kwf_Exception("Either _model has to be set or _fetchData has to be overwritten.");
        }

        $select = $this->_getSelect();
        if (is_null($select)) return null; //wenn _getSelect null zurückliefert nichts laden
        $select->limit($limit, $start);
        if ($order) $select->order($order);
        return $this->_model->getRows($select);
    }

    protected function _fetchCount()
    {
        if (!isset($this->_model)) {
            return count($this->_fetchData(null, 0, 0));
        }

        $select = $this->_getSelect();
        if (is_null($select)) return 0;
        return $this->_model->countRows($select);
    }

    public function jsonDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $row = $this->_model->getRow($id);
        if (!$row) throw new Kwf_Exception("No entry with id '$id' found");
        $this->_beforeDelete($row);
        $row->delete();
        $this->view->id = $id;
        $this->_afterDelete();
    }

    protected function _beforeDelete(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterDelete()
    {
    }
}
