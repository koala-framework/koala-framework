<?php
class Kwc_List_ChildPages_Teaser_Trl_AdminModel extends Kwf_Model_Data_Abstract
{
    public function setComponentId($componentId)
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        $s = new Kwf_Component_Select();
        $s->ignoreVisible();
        $s->whereGenerator('child');
        $i = 0;
        foreach ($c->getChildComponents($s) as $c) {
            $this->_data[$c->componentId] = array(
                'id' => $c->chained->row->id,
                'pos' => $i++,
                'component_id' => $componentId,
                'row' => $c->row,
                'name' => $c->targetPage->name
            );
        }
    }
}
