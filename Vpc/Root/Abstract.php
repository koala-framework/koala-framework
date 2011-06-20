<?php
class Vpc_Root_Abstract extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(),
            'inherit' => true,
            'priority' => 0
        );
        $ret['generators']['title'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Box_Title_Component',
            'inherit' => true,
            'priority' => 0
        );
        $ret['componentName'] = trlVps('Root');
        return $ret;
    }

    public function formatPath($parsedUrl)
    {
        $b = Zend_Registry::get('config')->server->domain == $parsedUrl['host'];
        if (!$b && isset($parsedUrl['port'])) {
            $b = Zend_Registry::get('config')->server->domain == $parsedUrl['host'].':'.$parsedUrl['port'];
        }
        if (!$b) {
            $p =  Zend_Registry::get('config')->server->noRedirectPattern;
            if (!$p) return null;
            if (!preg_match('/'.$p.'/', $parsedUrl['host'])) {
                return null;
            }
        }
        return $parsedUrl['path'];
    }

    public function getPageByUrl($path, $acceptLangauge)
    {
        if ($path == '') {
            $ret = $this->getData()->getChildPage(array('home' => true), array());
        } else {
            foreach (Vpc_Abstract::getComponentClasses() as $c) {
                if (Vpc_Abstract::getFlag($c, 'shortcutUrl')) {
                    $ret = call_user_func(array($c, 'getDataByShortcutUrl'), $c, $path);
                    if ($ret) return $ret;
                }
            }
            $ret = $this->getData()->getChildPageByPath($path);
        }

        if ($ret && !$ret->isPage && Vps_Component_Abstract::getFlag($ret->componentClass, 'hasHome')) {
            $ret = $ret->getChildPage(array('home' => true), array());
        }
        return $ret;
    }
}
