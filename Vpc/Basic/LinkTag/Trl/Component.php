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
        $ret['child'] = $this->getData()->getChildComponent(array(
            'generator' => 'child'
        ));
        $ret['linkTag'] = $ret['child'];
        return $ret;
    }
}
