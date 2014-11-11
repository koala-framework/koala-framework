<?php
class Kwc_List_ChildPages_Teaser_ChildPageNameData extends Kwf_Data_Abstract
{
    public function load($row, array $info)
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->target_page_id, array('ignoreVisible'=>true, 'limit'=>1));
        return $c->name;
    }
}
