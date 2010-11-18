<?php
class Vpc_Posts_Write_Preview_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'ExtDelayedTask';
        $ret['assets']['files'][] = 'vps/Vpc/Posts/Write/Preview/Component.js';

        $ret['placeholder']['preview'] = trlVpsStatic('Preview').':';
        // es wird von der eigenen komponente aus so lange nach oben gesucht
        // bis bis ein parentNode in irgendeiner unterebene ein child hat,
        // das mit sourceSelector übereinstimmt
        $ret['sourceSelector'] = 'textarea';
        $ret['textClass'] = 'text';

        $ret['cssClass'] = 'webStandard';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['detailClasses'] = self::getCssClass(
            $this->getData()->parent->getComponent()->getPostDirectoryClass()
        );
        $ret['sourceSelector'] = $this->_getSetting('sourceSelector');
        $ret['textClass'] = $this->_getSetting('textClass');
        return $ret;
    }

}
