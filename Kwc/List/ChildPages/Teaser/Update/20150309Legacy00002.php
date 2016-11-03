<?php
class Kwc_List_ChildPages_Teaser_Update_20150309Legacy00002 extends Kwf_Update
{
    public function postUpdate()
    {
        $cmps = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_List_ChildPages_Teaser_Component',
                    array('ignoreVisible'=>true)
                );
        $num = 0;
        foreach ($cmps as $cmp) {
            $num++;
            if (count($cmps) > 20) echo "updating List ChildPages [$num/".count($cmps)."]...\n";

            Kwf_Model_Abstract::getInstance('Kwc_List_ChildPages_Teaser_Model')->updatePages($cmp);

            $s = new Kwf_Model_Select();
            $s->whereEquals('component_id', $cmp->dbId);
            foreach (Kwf_Model_Abstract::getInstance('Kwc_List_ChildPages_Teaser_Model')->getRows($s) as $row) {
                $childPage = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->target_page_id, array('ignoreVisible'=>true, 'limit'=>1));
                $row->visible = isset($childPage->row) && isset($childPage->row->visible) ? $childPage->row->visible : true;
                $row->save();
            }
            Kwf_Model_Abstract::clearAllRows();
        }
    }
}

