<?php
/**
 * This controller is used to display a tree structure.
 *
 * The sync-prefix means that this controller loads all data at once, so children are always loaded.
 * 
 *
 * @package 
 */
abstract class Kwf_Controller_Action_Auto_Synctree extends Kwf_Controller_Action_Auto_Abstract
{
    const ADD_LAST = 0;
    const ADD_FIRST = 1;

    protected $_primaryKey;
    protected $_table;
    protected $_tableName;
    protected $_model;
    /**
    * The model has needs to be a subclass of Kwf_Model_Tree
    */
    protected $_modelName;
    protected $_filters;

    protected $_icons = array (
        'root'      => 'folder',
        'default'   => 'table',
        'edit'      => 'table_edit',
        'invisible' => 'table_key',
        'add'       => 'table_add',
        'delete'    => 'table_delete'
    );
    protected $_textField = 'name';
    protected $_parentField = 'parent_id';
    /**
    * Change this array to your needs.
    * remove an entry to remove the button.
    * false to disable the button.
    */
    protected $_buttons = array(
        'add'       => true,
        'edit'      => false,
        'delete'    => true,
        'invisible' => null,
        'reload'    => true
    );
    protected $_rootText = 'Root';
    protected $_rootVisible = true;
    protected $_hasPosition; // Gibt es ein pos-Feld
    /**
    * Use this field to set a controller to add/edit a single entry of this tree
    * it should look like this:
    * protected $_editDialog = array(
    *    'controllerUrl' => 'url',
    * )
    * The url should match the entry in Acl.php
    */
    protected $_editDialog;
    private $_openedNodes = array();
    protected $_addPosition = self::ADD_FIRST;
    /**
    * Set this field to true to enable drag and drop for tree-items
    */
    protected $_enableDD;
    protected $_defaultOrder;
    protected $_rootParentValue = null;

    /**
    * This method is called when the url is requested without params and sub-paths
    */
    public function indexAction()
    {
        $this->view->controllerUrl = $this->getRequest()->getBaseUrl().$this->getRequest()->getPathInfo();
        $this->view->xtype = 'kwf.autotreesync';
    }

    public function setTable($table)
    {
        $this->_model = new Kwf_Model_Db(array(
            'table' => $table
        ));
    }

    protected function _init() {}

    public function preDispatch()
    {
        parent::preDispatch();

        if (isset($this->_tableName)) {
            $this->_table = new $this->_tableName();
        } else if (isset($this->_modelName)) {
            $this->_model = new $this->_modelName();
        }
        if (isset($this->_table)) {
            $this->_model = new Kwf_Model_Db(array('table' => $this->_table));
        }
        if (!isset($this->_model)) {
            throw new Kwf_Exception('$_model oder $_modelName not set');
        }
        if (is_string($this->_model)) {
            $this->_model = Kwf_Model_Abstract::getInstance($this->_model);
        }

        $this->_filters = new Kwf_Collection();

        $this->_init();

        foreach ($this->_filters as $filter) $filter->setModel($this->_model);

        // PrimaryKey setzen
        if (!isset($this->_primaryKey)) {
            $this->_primaryKey = $this->_model->getPrimaryKey();
            if (is_array($this->_primaryKey)) {
                $this->_primaryKey = $this->_primaryKey[1];
            }
        }

        $cols = $this->_model->getColumns();
        // Invisible-Button hinzufügen falls nicht überschrieben und in DB
        if (array_key_exists('invisible', $this->_buttons) &&
            is_null($this->_buttons['invisible']) &&
            in_array('visible', $cols))
        {
            $this->_buttons['invisible'] = true;
        }

        // Pos-Feld
        if (!isset($this->_hasPosition)) {
            $this->_hasPosition = in_array('pos', $cols);
        }
        if ($this->_hasPosition && !in_array('pos', $cols)) {
            throw new Kwf_Exception("_hasPosition is true, but 'pos' does not exist in database");
        }

        foreach ($this->_icons as $k=>$i) {
            if (is_string($i)) {
                $this->_icons[$k] = new Kwf_Asset($i);
            }
        }

        if (is_string($this->_defaultOrder)) {
            $this->_defaultOrder = array(
                'field' => $this->_defaultOrder,
                'direction'   => 'ASC'
            );
        }

        // Falls Filter einen Default-Wert hat:
        // - GET query-Parameter setzen,
        // - Im JavaScript nach rechts verschieben und Defaultwert setzen
        foreach ($this->_filters as $filter) {
            if ($filter instanceof Kwf_Controller_Action_Auto_Filter_Text) continue;
            $param = $filter->getParamName();
            if ($filter->getConfig('default') && !$this->_getParam($param)) {
                $this->_setParam($param, $filter->getConfig('default'));
            }
        }
    }

    public function jsonMetaAction()
    {
        $this->view->helpText = $this->getHelpText();
        $this->view->icons = array();
        foreach ($this->_icons as $k=>$i) {
            $this->view->icons[$k] = $i->__toString();
        }
        $filters = array();
        foreach ($this->_filters as $filter) {
            $filters[] = $filter->getExtConfig();
        }
        $this->view->filters = $filters;
        $this->view->rootText = $this->_rootText;
        $this->view->rootVisible = $this->_rootVisible;
        $this->view->buttons = $this->_buttons;
        $this->view->editDialog = $this->_editDialog;
        if (is_null($this->_enableDD)) {
            $this->view->enableDD = $this->_hasPosition;
        } else {
            $this->view->enableDD = $this->_enableDD;
            if (!$this->_hasPosition) {
                $this->view->dropConfig = array('appendOnly' => true);
            }
        }
    }

    public function jsonDataAction()
    {
        $parentId = $this->_getParam('node');
        $this->_saveSessionNodeOpened($parentId, true);
        $this->_saveNodeOpened();

        $method = '_formatNodes';
        foreach ($this->_filters as $filter) {
            if ($this->_getParam($filter->getParamName())) $method = '_filterNodes';
        }
        $this->view->nodes = $this->$method();
    }

    public function jsonNodeDataAction()
    {
        $id = $this->getRequest()->getParam('node');
        $row = $this->_model->find($id)->current();
        if ($row) {
            $this->view->data = $this->_formatNode($row);
        } else {
            throw new Kwf_ClientException('Couldn\'t find row with id ' . $id);
        }
    }

    /**
     * @deprecated
     */
    protected function _getTreeWhere($parentRow = null)
    {
        return $this->_getWhere();
    }
    /**
     * @deprecated
     */
    protected function _getWhere()
    {
        return array();
    }

    protected function _formatNodes($parentRow = null)
    {
        $nodes = array();
        $rows = $this->_fetchData($parentRow);
        foreach ($rows as $row) {
            $node = $this->_formatNode($row);
            $childNodes = $this->_formatNodes($row);
            if (count($childNodes) == 0) $node['expanded'] = true;
            $node['children'] = $childNodes;

            $nodes[] = $node;
        }
        return $nodes;
    }

    protected function _getQueryExpression($query)
    {
        $containsExpression = array();
        foreach ($this->_queryFields as $queryField) {
            $containsExpression[] = new Kwf_Model_Select_Expr_Contains($queryField, $query);
        }
        return new Kwf_Model_Select_Expr_Or($containsExpression);
    }

    protected function _filterNodes()
    {
        $select = $this->_getSelect();

        //erzeugen von Filtern
        foreach ($this->_filters as $filter) {
            if ($filter->getSkipWhere()) continue;
            $select = $filter->formatSelect($select, $this->_getAllParams());
        }

        $rows = $this->_model->getRows($select);

        $plainNodes = array();
        foreach ($rows as $row) {
            $primaryKey = $this->_primaryKey;

            $parentValue = $this->_getParentId($row);
            $pV = is_null($parentValue) ? 0 : $parentValue;
            $primaryValue = $row->$primaryKey;
            if (!isset($plainNodes[$pV][$primaryValue])) {
                $node = $this->_formatNode($row);
                $node['leaf'] = true;
                $node['allowDrag'] = false;
                $node['filter'] = true;
                $node['sort'] = $select->getPart('order');
                $plainNodes[$pV][$row->$primaryKey] = $node;
            }
            $plainNodes[$pV][$row->$primaryKey]['disabled'] = false;
            while ($parentValue) {
                $parentRow = $this->_model->getRow($parentValue);
                if (!$parentRow) {
                    $parentValue = null;
                    continue;
                }
                $parentValue = $this->_getParentId($parentRow);
                $pV = is_null($parentValue) ? 0 : $parentValue;
                $primaryValue = $parentRow->$primaryKey;
                if (!isset($plainNodes[$pV][$primaryValue])) {
                    $node = $this->_formatNode($parentRow);
                    $node['disabled'] = true;
                    $node['expanded'] = true;
                    $node['expanded'] = true;
                    $node['allowDrag'] = false;
                    $node['filter'] = true;
                    $node['sort'] = $select->getPart('order');
                    $plainNodes[$pV][$primaryValue] = $node;
                }
            }
        }
        return $this->_structurePlainNodes($plainNodes, 0);
    }

    protected function _getParentId($row)
    {
        return $row->{$this->_parentField};
    }

    private function _structurePlainNodes($nodes, $parentValue)
    {
        $ret = array();
        if (!isset($nodes[$parentValue])) return array();
        foreach ($nodes[$parentValue] as $primaryValue => $node) {
            $node['children'] = $this->_structurePlainNodes($nodes, $primaryValue);
            $ret[] = $node;
        }
        $fieldname = $node['sort'][0]['field'];
        if ($node && isset($node['data'][$fieldname])) {
            usort($ret, array("Kwf_Controller_Action_Auto_Synctree", "_sortFilteredNodes"));
        }
        foreach ($ret as &$r) unset($r['sort']);
        return $ret;
    }

    private static function _sortFilteredNodes($a, $b)
    {
        foreach ($a['sort'] as $s) {
            $field = $s['field'];
            if (!isset($a['data'][$field])) continue;
            $value1 = ord(strtolower($a['data'][$field]));
            $value2 = ord(strtolower($b['data'][$field]));
            if ($value1 != $value2) {
                if ($s['direction'] == 'DESC') {
                    return $value1 > $value2;
                } else {
                    return $value1 > $value2;
                }
            }
        }
        return 0;
    }

    protected function _fetchData($parentRow)
    {
        $select = $this->_getSelect();
        if ($this->_model instanceof Kwf_Model_Tree_Interface) {
            if (!$parentRow) {
                return $this->_model->getRootNodes($select);
            } else {
                return $parentRow->getChildNodes($select);
            }
        } else {
            $where = $this->_getTreeWhere($parentRow);
            foreach ($where as $w) {
                $select->where($w);
            }
            if (!$parentRow) {
                if (is_null($this->_rootParentValue)) {
                    $select->whereNull($this->_parentField);
                } else {
                    $select->whereEquals($this->_parentField, $this->_rootParentValue);
                }
            } else {
                $select->whereEquals($this->_parentField, $parentRow->{$this->_primaryKey});
            }
            return $this->_model->getRows($select);
        }
    }

    /**
    * override this method to handle the output of this view. Just return the
    * select statement which serves your needs.
    */
    protected function _getSelect()
    {
        $select = $this->_model->select();
        if ($this->_hasPosition) {
            $select->order('pos');
        } else if (!$select->hasPart('order') && $this->_defaultOrder) {
            $select->order(
                $this->_defaultOrder['field'],
                $this->_defaultOrder['direction']
            );
        }
        return $select;
    }

    protected function _formatNode($row)
    {
        $data = array();
        $primaryKey = $this->_primaryKey;
        $data['id'] = $row->$primaryKey;
        if (!$row->hasColumn($this->_textField)) {
            throw new Kwf_Exception("Column '{$this->_textField}' not found, please overwrite \$_textField");
        }
        $data['text'] = $row->{$this->_textField};
        $data['data'] = $row->toArray();
        $data['leaf'] = false;
        $data['visible'] = true;
        $data['uiProvider'] = 'Kwf.Tree.Node';
        if (isset($row->visible) && $row->visible == '0') { //TODO visible nicht hardcodieren
            $data['visible'] = false;
            $data['bIcon'] = $this->_icons['invisible']->__toString();
        } else {
            $data['visible'] = true;
            $data['bIcon'] = $this->_icons['default']->__toString();
        }
        $openedNodes = $this->_saveSessionNodeOpened(null, null);
        $data['uiProvider'] = 'Kwf.Tree.Node';
        $id = $row->$primaryKey;
        if ($openedNodes == 'all' ||
            (isset($openedNodes[$id]) && $openedNodes[$id]) ||
            isset($this->_openedNodes[$id]) ||
            $this->_getParam('openedId') == $id
        ) {
            $data['expanded'] = true;
        } else {
            $data['expanded'] = false;
        }
        $data['expandRequest'] = true;
        return $data;
    }

    protected function _saveSessionNodeOpened($id, $activate)
    {
        $session = new Kwf_Session_Namespace('admin');
        $key = 'treeNodes_' . get_class($this);
        if ($this->_getParam('openedId')) $session->$key = array();
        $ids = is_array($session->$key) ? $session->$key : array();
        if ($id) {
            $ids[$id] = $activate;
            $session->$key = $ids;
        }
        return $ids;
    }

    protected function _saveNodeOpened()
    {
        $openedId = $this->_getParam('openedId');
        $this->_openedNodes = array();
        while ($openedId) {
            $row = $this->_model->find($openedId)->current();
            $this->_openedNodes[$openedId] = true;
            $field = $this->_parentField;
            $openedId = $row ? $row->$field : null;
        }
    }

    public function jsonVisibleAction()
    {
        $visible = $this->getRequest()->getParam('visible') == 'true';
        $id = $this->getRequest()->getParam('id');
        $row = $this->_model->find($id)->current();
        if (!$this->_hasPermissions($row, 'visible')) {
            throw new Kwf_Exception("Making visible/unvisible is not allowed for this row.");
        }
        $this->_changeVisibility($row);
        $row->save();
        $this->view->id = $id;
        $this->view->visible = $row->visible == '1';
        if (!isset($this->view->icon)) {
            $this->view->icon = $this->view->visible ?
                $this->_icons['default']->__toString() :
                $this->_icons['invisible']->__toString();
        }
    }

    protected function _changeVisibility(Kwf_Model_Row_Interface $row)
    {
        $row->visible = $row->visible == '0' ? '1' : '0';
    }

    public function jsonAddAction()
    {
        $insert[$this->_parentField] = $this->getRequest()->getParam('parentId');
        $insert[$this->_textField] = $this->getRequest()->getParam('name');
        if ($this->_hasPosition) {
            $insert['pos'] = 0;
        }
        $row = $this->_model->createRow($insert);
        if (!$this->_hasPermissions($row, 'add')) {
            throw new Kwf_Exception("Save is not allowed for this row.");
        }
        $row->save();
        $data = $this->_formatNode($row);
        foreach ($data as $k=>$i) {
            if ($i instanceof Kwf_Asset) {
                $data[$k] = $i->__toString();
            }
        }
        $this->view->data = $data;
    }

    public function jsonDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $row = $this->_model->find($id)->current();
        if (!$row) throw new Kwf_Exception("No entry with id '$id' found");
        if (!$this->_hasPermissions($row, 'delete')) {
            throw new Kwf_Exception("Delete is not allowed for this row.");
        }
        $this->_beforeDelete($row);
        $row->delete();
        $this->view->id = $id;
    }

    public function jsonMoveAction()
    {
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $point  = $this->getRequest()->getParam('point');

        $parentField = $this->_parentField;
        $row = $this->_model->getRow($source);
        if (!$this->_hasPermissions($row, 'move')) {
            throw new Kwf_Exception("Moving this node is not allowed.");
        }
        $targetRow = $this->_model->getRow($target);
        if (!$this->_hasPermissions($targetRow, 'moveTo')) {
            throw new Kwf_Exception("Moving here is not allowed.");
        }
        $this->_beforeSaveMove($row);

        if ($point == 'append') {
            if (is_numeric($target) && (int)$target == 0) $target = null;

            if (!is_null($target)) {
                $targetRow = $this->_model->getRow($target);
            }
            if (is_null($target) ||
                ($targetRow && $targetRow->$parentField != $source && $target != $source)
            ) {
                $row->$parentField = $target;
                if ($this->_hasPosition) {
                    $row->pos = '1';
                }
            } else {
                $this->view->error = trlKwf('Cannot move here. View has been reloaded, please try again.');
            }
        } else {
            if ($targetRow) {
                if ($this->_hasPosition) {
                    $targetPosition = $targetRow->pos;
                    if ($point == 'below') {
                        $targetPosition++;
                    }
                    if ($row->$parentField == $targetRow->$parentField &&
                        $row->pos < $targetRow->pos
                    ) {
                         $targetPosition--;
                    }
                    $row->pos = $targetPosition;
                }
                $row->$parentField = $targetRow->$parentField;
            } else {
                $this->view->error = trlKwf('Cannot move here.');
            }
        }
        $row->save();

        $row = $this->_model->find($row->id)->current();
        $primaryKey = $this->_model->getPrimaryKey();
        $before = null;
        $select = $this->_getSelect($this->_getTreeWhere());
        $parentValue = $row->$parentField;
        if (!$parentValue) {
            if (is_null($this->_rootParentValue)) {
                $select->whereNull($this->_parentField);
            } else {
                $select->whereEquals($this->_parentField, $this->_rootParentValue);
                $parentValue = $this->_rootParentValue;
            }
        } else {
            $select->whereEquals($this->_parentField, $parentValue);
        }
        foreach ($this->_model->fetchAll($select) as $r) {
            if ($before === true) $before = $r->$primaryKey;
            if ($r->$primaryKey == $source) {
                $before = true;
            }
        }
        if ($before === true) $before = null;

        $this->view->parent = $parentValue;
        $this->view->node = $source;
        $this->view->before = $before;
    }

    protected function _beforeSaveMove($row) {}
    protected function _beforeDelete(Kwf_Model_Row_Interface $row) {}

    public function jsonCollapseAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->_saveSessionNodeOpened($id, false);
    }

    public function jsonExpandAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->_saveSessionNodeOpened($id, true);
    }

    protected function _hasPermissions($row, $action)
    {
        return true;
    }
}
