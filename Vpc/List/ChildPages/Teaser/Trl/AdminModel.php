<?php
class Vpc_List_ChildPages_Teaser_Trl_AdminModel extends Vps_Model_Data_Abstract
{
    public function setComponentId($componentId)
    {
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($componentId, array('ignoreVisible'=>true));
        $s = new Vps_Component_Select();
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
