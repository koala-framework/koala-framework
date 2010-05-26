<?php
class Vpc_Box_Tags_RelatedNews_Component extends Vpc_Box_Tags_RelatedPages_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($ret['related'] as $k=>$i) {
            if (!is_instance_of($i->componentClass, 'Vpc_News_Detail_Component')) {
                unset($ret['related'][$k]);
            }
        }
        return $ret;
    }
}
