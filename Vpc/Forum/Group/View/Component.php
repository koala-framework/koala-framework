<?php
class Vpc_Forum_Group_View_Component extends Vpc_Directories_List_ViewPage_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($ret['items'] as &$item) {
            foreach ($item->getComponent()->getThreadVars() as $key => $val) {
                $item->$key = $val;
            }
        }
        return $ret;
    }
}
