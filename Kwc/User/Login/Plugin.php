<?php
//this plugin replaces %redirect% and %sampleLogin% dynamic (so viewCache can be enabled)
class Kwc_User_Login_Plugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewAfterChildRender
{
    public function processOutput($output, $renderer)
    {
        if (strpos($output, '%redirect%') !== false) {
            $r = isset($_GET['redirect']) ? $_GET['redirect'] : '';
            $output = str_replace('%redirect%', urlencode($r), $output);
        }

        if (strpos($output, '%sampleLogin%') !== false) {
            $absoluteUrl = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($this->_componentId, array('ignoreVisible'=>true))
                ->getAbsoluteUrl();
            $sampleLinks = array();
            foreach (Kwf_Registry::get('userModel')->getAuthMethods() as $auth) {
                if ($auth instanceof Kwf_User_Auth_Interface_Redirect) {
                    $sampleLinks = array_merge($sampleLinks, $auth->createSampleLoginLinks($absoluteUrl));
                }
            }
            $sampleLogin = '';
            foreach ($sampleLinks as $link) {
                $sampleLogin .= "<li><a href=\"".htmlspecialchars($link['url'])."\">".htmlspecialchars($link['name'])."</a></li>\n";
            }
            $sampleLogin = "<ul>$sampleLogin</ul>\n";
            $output = str_replace('%sampleLogin%', $sampleLogin, $output);
        }
        return $output;
    }
}
