<?php
class Vpc_Basic_LinkTag_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_Data';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['linkTag'] = $this->getData()->getChildComponent(array(
            'generator' => 'link'
        ));
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $link = $this->getData()->getChildComponent('-link');
        if ($link) {
            $ret['linkTagLink']['componentId'] = $link->componentId;
        }
        return $ret;
    }
}
