<?php
class Vpc_Formular_IndexController extends Vps_Controller_Action_Auto_Grid
{
   protected $_columns = array(
            array('dataIndex' => 'component_class',
                  'header'    => 'Komponente',
                  'width'     => 300),
            array('dataIndex' => 'visible',
                  'header'    => 'Sichtbar',
                  'width'     => 50,
                  'editor'    => 'Checkbox',
                  ),
      array('dataIndex' => 'name',
                  'header'    => 'Bezeichnung',
                  'width'     => 150,
                  'editor'    => 'TextField',
                  ),
            array('dataIndex' => 'mandatory',
                  'header'    => 'Verpflichtend',
                  'width'     => 80,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'no_cols',
                  'header'    => 'noCols',
                  'width'     => 50,
                  'editor'    => 'Checkbox',
                  ),
            array('dataIndex' => 'page_id',
                  'header'    => 'page_id',
                  'width'     => 50,
                  'hidden'   =>  true,
                  ),
            array('dataIndex' => 'id',
                  'header'    => 'id',
                  'width'     => 50,
                  'hidden'   =>  true,
                  )

            );
    protected $_buttons = array('save'=>true,
                                    'add'=>true,
                                    'delete'=>true);
    protected $_paging = 0;
    protected $_defaultOrder = 'pos';
    protected $_tableName = 'Vpc_Formular_IndexModel';

    public function indexAction()
    {
        $components = array();
        foreach (Vpc_Setup_Abstract::getAvailableComponents('Formular/') as $component) {
            if ($component != 'Vpc_Formular_Index') {
                $components[$component] = $component;
            }
        }

        $cfg = array();
        $cfg['components'] = $components;
        $this->view->ext('Vpc.Formular.Index', $cfg);
    }

    public function jsonIndexAction()
    {
        $this->indexAction();
    }

    protected function _beforeSave($row)
    {
        $row->page_id = $this->component->getDbId();
        $row->component_key = $this->component->getComponentKey();
        $row->pos = $this->_getPosition();
        $row->save();
    }

    protected function _getWhere()
    {
    	$where = parent::_getWhere();
    	$where['page_id = ?'] = $this->component->getDbId();
    	$where['component_key = ?'] = $this->component->getComponentKey();
    	return $where;
    }

    private function _getPosition ()
    {
        $rows = $this->_table->fetchAll(array('page_id = ?'  => $this->component->getDbId(),
                                             'component_key = ?' => $this->component->getComponentKey()));
        $ids = array();
        foreach ($rows as $rowKey => $rowData){
            $id =$rowData->pos;
            $ids[] = $id;
        }
        rsort($ids);
        if ($ids == array()) $id = 1;
        else $id = $ids[0] + 1;
        return $id;
    }
}