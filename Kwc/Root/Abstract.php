<?php
class Kwc_Root_Abstract extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(),
            'inherit' => true,
            'priority' => 0
        );
        $ret['generators']['title'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Box_Title_Component',
            'inherit' => true,
            'priority' => 0
        );
        $ret['componentName'] = trlKwf('Root');
        $ret['contentWidth'] = 600;
        $ret['contentWidthBoxSubtract'] = array();
        $ret['flags']['hasBaseProperties'] = true;
        return $ret;
    }

    public function formatPath($parsedUrl)
    {
        if (!Kwf_Config::getValue('server.domain')) {
            //domain is optional (but recommended)
            //for easy setup of examples just ignore the domain (as we don't have anything to compare to anyway)
            return $parsedUrl['path'];
        }
        $b = Kwf_Config::getValue('server.domain') == $parsedUrl['host'];
        if (!$b && isset($parsedUrl['port'])) {
            $b = Kwf_Config::getValue('server.domain') == $parsedUrl['host'].':'.$parsedUrl['port'];
        }
        if (!$b) {
            $p =  Kwf_Config::getValue('server.noRedirectPattern');
            if (!$p) return null;
            if (!preg_match('/'.$p.'/', $parsedUrl['host'])) {
                return null;
            }
        }
        return $parsedUrl['path'];
    }

    public function getPageByUrl($path, $acceptLanguage)
    {
        return self::getChildPageByPath($this->getData(), $path);
    }

    public static function getChildPageByPath($component, $path)
    {
        if ($path == '') {
            $ret = $component->getChildPage(array('home' => true), array());
        } else {
            foreach (Kwc_Abstract::getComponentClasses() as $c) {
                if (Kwc_Abstract::getFlag($c, 'shortcutUrl')) {
                    $ret = call_user_func(array($c, 'getDataByShortcutUrl'), $c, $path);
                    if ($ret) return $ret;
                }
            }
            $ret = $component->getChildPageByPath($path);
        }

        if ($ret && !$ret->isPage && Kwf_Component_Abstract::getFlag($ret->componentClass, 'hasHome')) {
            $ret = $ret->getChildPage(array('home' => true), array());
        }
        return $ret;
    }

    public function getBaseProperty($propertyName)
    {
        if ($propertyName == 'language') {
            return Kwf_Trl::getInstance()->getWebCodeLanguage();
        } else if ($propertyName == 'domain') {
            return Kwf_Config::getValue('server.domain');
        } else if ($propertyName == 'money.decimals') {
            return 2;
        } else if ($propertyName == 'money.decimalSeparator') {
            return trlcKwf('decimal separator', ".");
        } else if ($propertyName == 'money.thousandSeparator') {
            return trlcKwf('thousands separator', ",");
        } else {
            return Kwf_Config::getValue($propertyName);
        }
    }
}
