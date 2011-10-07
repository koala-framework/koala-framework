<?php
class Kwc_Abstract_List_Trl_AdminModel extends Kwf_Model_Data_Abstract
{
    protected $_translateFields = array('visible');

    public function setComponentId($componentId)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        $s = new Kwf_Component_Select();
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

    public function update(Kwf_Model_Row_Interface $row, $rowData)
    {
        parent::update($row, $rowData);
        foreach ($this->_translateFields as $tf) {
            $rowData['row']->{$tf} = $row->{$tf};
        }
        $rowData['row']->save();
    }
}
