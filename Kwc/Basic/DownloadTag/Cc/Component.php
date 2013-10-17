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
        $retValid = self::VALID;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id);
        if (!$c) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));
            if (!$c) return self::INVALID;
            if (Kwf_Registry::get('config')->showInvisible) {
                //preview im frontend
                $retValid = self::VALID_DONT_CACHE;
            } else if (Kwf_Registry::get('acl')->isAllowedComponentById($id, $className, Kwf_Registry::get('userModel')->getAuthedUser())) {
                //paragraphs vorschau im backend
                $retValid = self::VALID_DONT_CACHE;
            }
        }
        while ($c) {
            foreach (Kwc_Abstract::getSetting($c->componentClass, 'plugins') as $plugin) {
                if (is_instance_of($plugin, 'Kwf_Component_Plugin_Interface_Login')) {
                    $plugin = new $plugin($id);
                    if ($plugin->isLoggedIn()) {
                        return self::VALID_DONT_CACHE;
                    } else {
                        return self::ACCESS_DENIED;
                    }
                }
            }
            if ($c->isPage) break;
            $c = $c->parent;
        }
        return $retValid;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
        $cls = $c->chained->componentClass;
        $cls = strpos($cls, '.') ? substr($cls, 0, strpos($cls, '.')) : $cls;
        return call_user_func(array($cls, 'getMediaOutput'), $c->chained->componentId, $type, $c->chained->componentClass);
    }
}
