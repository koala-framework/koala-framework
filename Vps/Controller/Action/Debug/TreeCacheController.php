<?php
class Vps_Controller_Action_Debug_TreeCacheController extends Vps_Controller_Action_Auto_Tree
{
    protected $_tableName = 'Vps_Dao_TreeCache';
    protected $_buttons = array('reload');

    protected $_parentField = 'parent_component_id';
    protected $_icons = array (
        'root'      => 'folder',
        'default'   => 'table',
        'edit'      => 'table_edit',
        'invisible' => 'table_key',
        'add'       => 'table_add',
        'delete'    => 'table_delete'
    );
    public function init()
    {
        parent::init();
        $this->_icons['page'] = 'page';
        $this->_icons['component'] = 'page_white_text_width';
    }

    protected function _formatNode($row)
    {
        $ret = parent::_formatNode($row);
        if ($row->url_match) {
            $ret['bIcon'] = $this->_icons['page']->__toString();
        } else {
            $ret['bIcon'] = $this->_icons['component']->__toString();
        }
        $ret['text'] = '';
        if ($row->name) $ret['text'] .= $row->name . ' - ';
        $ret['text'] = $row->component_class;
        $ret['text'] .= " ($row->component_id)";
        return $ret;
    }
}
