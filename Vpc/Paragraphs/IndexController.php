<?php
class Vpc_Paragraphs_IndexController extends Vps_Controller_Action_Auto_Grid
{
    protected $_columns = array(
            array('dataIndex' => 'component_class',
                  'header'    => 'Komponente',
                  'width'     => 300),
            array('dataIndex' => 'visible',
                  'header'    => 'Sichtbar')
            );
    protected $_buttons = array('save'=>true,
                                    'add'=>true,
                                    'delete'=>true);
    protected $_paging = 0;
    protected $_defaultOrder = 'pos';
    protected $_tableName = 'Vpc_Paragraphs_IndexModel';
    
    public function indexAction()
    {
        $ini = new Vps_Config_Ini('application/components.ini');
        foreach ($ini->toArray() as $component => $data) {
            $components[$component] = $data['name'];
        }
        
        $config = array('components' => $components);
        $this->view->ext('Vpc.Paragraphs.Index', $config);
    }
       
    public function jsonIndexAction()
    {
        $this->indexAction();
    }

    protected function _getWhere()
    {
        $where = array();
        $where[] = "page_id='" . $this->component->getDbId() . "'";
        $where[] = "component_key='" . $this->component->getComponentKey() . "'";
        return $where;
    }

}
