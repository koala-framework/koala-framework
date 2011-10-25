<?php
/**
 * Dieses Plugin zeigt falls der User eingeloggt ist die LoggedIn Unterkomponente an
 */
class Kwc_User_BoxWithoutLogin_IsLoggedInPlugin_Component extends Kwf_Component_Plugin_Login_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['loginForm']);
        $ret['validUserRoles'] = null;
        return $ret;
    }

    public function processOutput($output)
    {
        if (!$this->isLoggedIn()) {
            return $output;
        }
        $loggedIn = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId, array('ignoreVisible' => true))
            ->getChildComponent('-loggedIn');
        return $loggedIn->render();
    }
}
