<?php
class Kwc_Basic_DownloadTag_Cc_Component extends Kwc_Basic_LinkTag_Abstract_Cc_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['dataClass'] = 'Kwc_Basic_DownloadTag_Cc_Data';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['url'] = $this->getDownloadUrl();
        return $ret;
    }

    public function getDownloadUrl()
    {
        return $this->getData()->url;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValid($id);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
        $cls = $c->chained->componentClass;
        $cls = strpos($cls, '.') ? substr($cls, 0, strpos($cls, '.')) : $cls;
        return call_user_func(array($cls, 'getMediaOutput'), $c->chained->componentId, $type, $c->chained->componentClass);
    }
}
