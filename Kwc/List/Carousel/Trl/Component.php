<?php
class Kwc_List_Carousel_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        if(count($ret['listItems']) == 2 || count($ret['listItems']) == 3) {
            // Necessary as carousel slider needs at least 4 items to work
            $ret['listItems'] = array_merge($ret['listItems'], $ret['listItems']);
        }
        return $ret;
    }
}
