<?php
class Vpc_Advanced_SocialBookmarks_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['services'] = array(
            'wong', 'delicious', 'yigg', 'webnews', 'linkarena', 'google', 'digg'
        );
        return $ret;
    }
    
    protected function _getServices()
    {
        $ret = array(
            'mrwong' => array(
                'url' => 'http://www.mister-wong.de/index.php?action=addurl&bm_url={0}',
                'name' => 'Mister Wong'
            ),
            'delicious' => array(
                'url' => 'http://del.icio.us/post?url={0}',
                'name' => 'del.icio.us'
            ),
            'yigg' => array(
                'url' => 'http://yigg.de/neu?exturl={0}',
                'name' => 'Yigg'
            ),
            'webnews' => array(
                'url' => 'http://www.webnews.de/einstellen?url={0}',
                'name' => 'Webnews'
            ),
            'linkarena' => array(
                'url' => 'http://linkarena.com/bookmarks/addlink/?url={0}',
                'name' => 'LinkARENA'
            ),
            'google' => array(
                'url' => 'http://www.google.com/bookmarks/mark?op=add&hl=de&bkmk={0}',
                'name' => 'Google'
            ),
            'digg' => array(
                'url' => 'http://digg.com/register?url={0}',
                'name' => 'Digg'
            )
        );
        foreach ($ret as $key => &$r) {
            $ret[$key]['icon'] = "/assets/vps/images/socialbookmarks/$key.gif";
        }
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($this->_getSetting('services') as $service) {
            if (!isset($services[$service])) {
                throw new Vps_Exception('Service not found: ' . $service);
            }
            $services[$service]['url'] = str_replace('{0}', $_SERVER['SCRIPT_URI']);
            $ret['services'][] = $services[$service];
        }
        return $ret;
    }
}
