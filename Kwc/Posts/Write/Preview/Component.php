<?php
class Kwc_Posts_Write_Preview_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['placeholder']['preview'] = trlKwfStatic('Preview').':';
        // es wird von der eigenen komponente aus so lange nach oben gesucht
        // bis bis ein parentNode in irgendeiner unterebene ein child hat,
        // das mit sourceSelector übereinstimmt
        $ret['sourceSelector'] = 'textarea';
        $ret['textClass'] = 'text';

        $ret['rootElementClass'] = 'kwfUp-webStandard';

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['detailClasses'] = self::getRootElementClass(
            $this->getData()->parent->getComponent()->getPostDirectoryClass()
        );
        $ret['sourceSelector'] = $this->_getSetting('sourceSelector');
        $ret['textClass'] = $this->_getSetting('textClass');
        return $ret;
    }

}
