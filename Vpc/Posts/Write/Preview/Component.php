<?php
class Vpc_Posts_Write_Preview_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['files'][] = 'vps/Vpc/Posts/Write/Preview/Component.js';

        $ret['placeholder']['preview'] = trlVps('Preview:');
        // es wird von der eigenen komponente aus so lange nach oben gesucht
        // bis bis ein parentNode in irgendeiner unterebene ein child hat,
        // das mit sourceSelector übereinstimmt
        $ret['sourceSelector'] = 'textarea';

        $ret['cssClass'] = 'webStandard';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $dirGenerators = Vpc_Abstract::getSetting(
            $this->getData()->parent->getComponent()->getDirectoryComponent()->componentClass, 'generators'
        );
        $ret['detailCss'] = self::getCssClass($dirGenerators['detail']['component']);
        $ret['sourceSelector'] = $this->_getSetting('sourceSelector');
        return $ret;
    }

}
