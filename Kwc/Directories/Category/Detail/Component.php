<?php
class Kwc_Directories_Category_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['list'] = 'Kwc_Directories_Category_Detail_List_Component';
        $ret['flags']['hasComponentLinkModifiers'] = true;
        return $ret;
    }

    public function getComponentLinkModifiers()
    {
        $l = $this->getData()->getChildComponent('-list')->getComponent();
        $cnt = $l->getItemDirectory()->countChildComponents($l->getSelect());
        return array(
            array(
                'type' => 'appendText',
                'text' => ' ('.$cnt.')'
            )
        );
    }
}
