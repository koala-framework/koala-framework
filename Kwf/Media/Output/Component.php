<?php
class Kwf_Media_Output_Component
{
    /**
     * Helper function that can be used in Component implementing Kwf_Media_Output_IsValidInterface
     * to check if the component is visible to the current user
     */
    public static function isValid($id)
    {
        $writeCache = false;
        $cacheId = 'media-isvalid-component-'.$id;
        $plugins = Kwf_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            //success means it's VALID and we successfully fetched the $plugins
            $ret = Kwf_Media_Output_IsValidInterface::VALID;
        } else {
            $ret = Kwf_Media_Output_IsValidInterface::VALID;
            $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id);
            if (!$c) {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
                if (!$c) return Kwf_Media_Output_IsValidInterface::INVALID;
                if (Kwf_Component_Data_Root::getShowInvisible()) {
                    //preview im frontend
                    $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
                } else if (Kwf_Registry::get('acl')->isAllowedComponentById($id, $c->componentClass, Kwf_Registry::get('userModel')->getAuthedUser())) {
                    //paragraphs preview in backend
                    $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
                } else if (Kwf_Registry::get('acl')->isAllowedUser(Kwf_Registry::get('userModel')->getAuthedUser(), 'kwf_component_preview', 'view')) {
                    //perview user in frontend
                    $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
                } else {
                    return Kwf_Media_Output_IsValidInterface::ACCESS_DENIED;
                }
            }
            //$ret can be VALID or VALID_DONT_CACHE at this point

            $plugins = array();
            while ($c) {
                foreach (Kwc_Abstract::getSetting($c->componentClass, 'plugins') as $plugin) {
                    if (is_instance_of($plugin, 'Kwf_Component_Plugin_Interface_Login')) {
                        $plugins[] = array(
                            'plugin' => $plugin,
                            'id' => $c->componentId
                        );
                    }
                }
                if ($c->isPage) break;
                $c = $c->parent;
            }

            if ($ret == Kwf_Media_Output_IsValidInterface::VALID) {
                //only cache VALID, VALID_DONT_CACHE can't be cached
                $writeCache = true;
            }
        }

        foreach ($plugins as $p) {
            $plugin = $p['plugin'];
            $plugin = new $plugin($p['id']);
            if ($plugin->isLoggedIn()) {
                $ret = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
            } else {
                $ret = Kwf_Media_Output_IsValidInterface::ACCESS_DENIED;
                break;
            }
        }

        if ($writeCache && $ret == Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE) {
            //only cache VALID_DONT_CACHE, not VALID as it will be cached in Kwf_Media::getOutput
            //this cache doesn't need to be cleared, because of it's lifetime
            Kwf_Cache_Simple::add($cacheId, $plugins, 60*60);
        }
        return $ret;
    }
}
