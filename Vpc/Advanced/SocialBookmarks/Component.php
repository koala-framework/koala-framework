<?php
class Vpc_Advanced_SocialBookmarks_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Socal Bookmarks');
        $ret['ownModel'] = 'Vpc_Advanced_SocialBookmarks_Model';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $services = $this->_getServices();
/*        foreach ($ret as $key => &$r) {
            $ext = $key=='wong' ? 'png' : 'gif';
            $r['icon'] = "/assets/vps/images/socialbookmarks/$key.$ext";
        }*/
        foreach ($this->_getSetting('services') as $service) {
            if (!isset($services[$service])) {
                throw new Vps_Exception('Service not found: ' . $service);
            }
            $services[$service]['url'] = str_replace('{0}', $_SERVER['SCRIPT_URI'], $services[$service]['url']);
            $ret['services'][] = $services[$service];
        }
        return $ret;
    }
}
