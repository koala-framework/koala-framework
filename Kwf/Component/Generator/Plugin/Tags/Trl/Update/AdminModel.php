<?php
class Kwc_Directories_Category_Directory_Trl_AdminModel extends Kwf_Model_Data_Abstract
{
    public function setComponentId($componentId)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        $s = new Kwf_Component_Select();
        $s->ignoreVisible();
        $s->whereGenerator('detail');
        foreach ($c->getChildComponents($s) as $c) {
            $this->_data[$c->componentId] = array(
                'id' => $c->chained->row->id,
                'component_id' => $componentId,
                'original_name' => $c->chained->row->name,
                'name' => $c->row->name,
                'row' => $c->row,
                'visible' => $c->row->visible
            );
        }
    }

    public function update(Kwf_Model_Row_Interface $row, $rowData)
    {
        parent::update($row, $rowData);
        $rowData['row']->visible = $row->visible;
        $rowData['row']->name = $row->name;
        $rowData['row']->save();
    }
}
