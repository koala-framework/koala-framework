<?php
/**
 * Dieses Plugin zeigt falls der User eingeloggt ist die LoggedIn Unterkomponente an
 */
class Kwc_User_BoxWithoutLogin_IsLoggedInPlugin_Component extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeChildRender
{
    public function processOutput($output, $renderer)
    {
        $parts = explode('{kwfappendboxdata}', $output);
        if (count($parts) != 2) {
            throw new Kwf_Exception('Maybe no LoggedIn-Component ([\'loggedIn\']) set for BoxWithoutLogin-Component');
        }
        if (!$this->_isLoggedIn()) {
            // remove child-component-data from view-cache and return rest
            return $parts[0];
        }
        // get child-component-view-cache form data attached to view-cache and return html
        return $parts[1];
    }

    private function _isLoggedIn()
    {
        return Kwf_Setup::hasAuthedUser();
    }
}
