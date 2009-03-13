<?php
class Vpc_Basic_LinkTag_News_Component extends Vpc_Basic_LinkTag_Intern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_News_Data';
        $ret['componentName'] = trlVps('Link.to News');
        $ret['modelname'] = 'Vps_Component_FieldModel';
        return $ret;
    }
    
    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
// echo "-------------------------";
// var_dump($this->getData()->getLinkedData()->componentId);
// var_dump($this->getData()->getLinkedData()->componentClass);
// var_dump($this->getData()->getLinkedData()->getComponent()->getCacheVars());
        return $ret;
    }
}
