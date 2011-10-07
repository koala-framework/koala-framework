<?php
class Vpc_Basic_LinkTag_Cc_Component extends Vpc_Chained_Cc_Component
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
            'generator' => 'child'
        ));
        return $ret;
    }

    // TODO Cache
    /*
    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $link = $this->getData()->getChildComponent('-child');
        if ($link) {
            $ret['linkTagLink']['componentId'] = $link->componentId;
        }
        return $ret;
    }
    */
}
