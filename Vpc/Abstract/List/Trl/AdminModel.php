<?php
class Vpc_Abstract_List_Trl_AdminModel extends Vps_Model_Data_Abstract
{
    protected $_translateFields = array('visible');

    public function setComponentId($componentId)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        $s = new Vps_Component_Select();
        $s->ignoreVisible();
        $s->whereGenerator('child');
        foreach ($c->getChildComponents($s) as $c) {
            $this->_data[$c->componentId] = array(
                'id' => $c->chained->row->id,
                'component_id' => $componentId,
                'row' => $c->row,
                'pos' => $c->chained->row->pos
            );
            foreach ($this->_translateFields as $tf) {
                $this->_data[$c->componentId][$tf] = $c->row->{$tf};
            }
        }
    }

    public function update(Vps_Model_Row_Interface $row, $rowData)
    {
        parent::update($row, $rowData);
        foreach ($this->_translateFields as $tf) {
            $rowData['row']->{$tf} = $row->{$tf};
        }
        $rowData['row']->save();
    }
}
