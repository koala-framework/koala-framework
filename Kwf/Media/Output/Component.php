<?php
class Kwf_Media_Output_Component
{
    /**
     * Helper function that can be used in Component implementing Kwf_Media_Output_IsValidInterface
     * to check if the component is visible to the current user
     */
    public static function isValid($id)
    {
        $retValid = Kwf_Media_Output_IsValidInterface::VALID;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id);
        if (!$c) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));
            if (!$c) return Kwf_Media_Output_IsValidInterface::INVALID;
            if (Kwf_Component_Data_Root::getShowInvisible()) {
                //preview im frontend
                $retValid = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
            } else if (Kwf_Registry::get('acl')->isAllowedComponentById($id, $c->componentClass, Kwf_Registry::get('userModel')->getAuthedUser())) {
                //paragraphs vorschau im backend
                $retValid = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
            } else {
                return Kwf_Media_Output_IsValidInterface::ACCESS_DENIED;
            }
        }
        while ($c) {
            foreach (Kwc_Abstract::getSetting($c->componentClass, 'plugins') as $plugin) {
                if (is_instance_of($plugin, 'Kwf_Component_Plugin_Interface_Login')) {
                    $plugin = new $plugin($id);
                    if ($plugin->isLoggedIn()) {
                        $retValid = Kwf_Media_Output_IsValidInterface::VALID_DONT_CACHE;
                    } else {
                        $retValid = Kwf_Media_Output_IsValidInterface::ACCESS_DENIED;
                        break 2;
                    }
                }
            }
            if ($c->isPage) break;
            $c = $c->parent;
        }
        return $retValid;
    }
}
