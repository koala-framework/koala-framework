<?php
class Vpc_News_Directory_Trl_Controller_TrlData extends Vps_Data_Abstract
{
    public $componentId;
    public function load($row)
    {
        $r = Vps_Model_Abstract::getInstance('Vpc_News_Directory_Trl_Model')
            ->getRow($this->componentId.'_'.$row->id);
        if (!$r) {
            $r = Vps_Model_Abstract::getInstance('Vpc_News_Directory_Trl_Model')->createRow();
            $r->component_id = $this->componentId.'_'.$row->id;
        }
        if ($r->{$this->getFieldname()}) {
            return $r->{$this->getFieldname()};
        } else if ($this->getFieldname() != 'visible') {
            return $row->{$this->getFieldname()};
        }
        return null;
    }

    public function save(Vps_Model_Row_Interface $row, $data)
    {
        $r = Vps_Model_Abstract::getInstance('Vpc_News_Directory_Trl_Model')
            ->getRow($this->componentId.'_'.$row->id);
        if (!$r) {
            $r = Vps_Model_Abstract::getInstance('Vpc_News_Directory_Trl_Model')->createRow();
            $r->component_id = $this->componentId.'_'.$row->id;
        }
        $r->{$this->getFieldname()} = $data;
        $r->save();
    }
}

class Vpc_News_Directory_Trl_Controller extends Vpc_Directories_Item_Directory_Trl_Controller
{
    protected $_modelName = 'Vpc_News_Directory_Model';
    protected $_defaultOrder = array('field' => 'publish_date', 'direction' => 'DESC');

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('id'));

        $data = new Vpc_News_Directory_Trl_Controller_TrlData('title');
        $data->componentId = $this->_getParam('componentId');
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 300))
            ->setData($data);
        $this->_columns->add(new Vps_Grid_Column_Button('properties', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper.png')
            ->setTooltip(trlVps('Properties'));
        $this->_columns->add(new Vps_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper_go.png')
            ->setTooltip(trlVps('Edit News'));
        $this->_columns->add(new Vps_Grid_Column_Date('publish_date', trlVps('Publish Date')));

        $data = new Vpc_News_Directory_Trl_Controller_TrlData('visible');
        $data->componentId = $this->_getParam('componentId');
        $this->_columns->add(new Vps_Grid_Column_Visible('visible'))
            ->setData($data);
    }

}
