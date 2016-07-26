<?php
class Kwc_Box_Assets_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['language'] = $this->getData()->getLanguage();
        $ret['assetsPackages'] = array(Kwf_Assets_Package_ComponentFrontend::getInstance());

        $packageNames = array();
        $page = $this->getData()->getPage();
        if (Kwc_Abstract::getFlag($page->componentClass, 'assetsPackage')) {
            $packageName = Kwc_Abstract::getFlag($page->componentClass, 'assetsPackage');
            if ($packageName && !in_array($packageName, $packageNames)) {
                $packageNames[] = $packageName;
            }
        }
        foreach ($page->getRecursiveChildComponents(array('flags'=>array('assetsPackage'=>true), 'page'=>false)) as $d) {
            $packageName = Kwc_Abstract::getFlag($d->componentClass, 'assetsPackage');
            if ($packageName && !in_array($packageName, $packageNames)) {
                $packageNames[] = $packageName;
            }
        }
        $d = $page;
        while ($d) {
            if (Kwc_Abstract::getFlag($d->componentClass, 'assetsPackage')) {
                $packageName = Kwc_Abstract::getFlag($d->componentClass, 'assetsPackage');
                if ($packageName && !in_array($packageName, $packageNames)) {
                    $packageNames[] = $packageName;
                }
            }
            $d = $d->parent;
        }
        foreach ($packageNames as $packageName) {
            if ($packageName != 'Default') {
                $ret['assetsPackages'][] = Kwf_Assets_Package_ComponentPackage::getInstance($packageName);
            }
        }
        return $ret;
    }

    /**
     * @deprecated
     */
    protected final function _getSection()
    {
    }

    public function injectIntoRenderedHtml($html)
    {
        $startPos = strpos($html, '<!-- assets -->');
        $endPos = strpos($html, '<!-- /assets -->')+16;
        $assets = substr($html, $startPos, $endPos-$startPos-16);

        $loadedAssets = array();
        foreach (self::_parseAssets($assets) as $i) {
            $loadedAssets[] = $i['assetUrl'];
        }

        $lightboxAssets = $this->getData()->render();
        foreach (self::_parseAssets($lightboxAssets) as $i) {
            if (!in_array($i['assetUrl'], $loadedAssets)) {
                $assets .= $i['html']."\n";
            }
        }
        $html = substr($html, 0, $startPos)
                .$assets.'<!-- /assets -->'
                .substr($html, $endPos);
        return $html;
    }

    private static function _parseAssets($html)
    {
        $ret = array();
                            //assumption: one asset spans across exactly one line
        if (preg_match_all('#.*(/assets/[^"\']+).*#', $html, $m)) {
            foreach ($m[0] as $k=>$html) {
                $ret[] = array(
                    'assetUrl' => $m[1][$k],
                    'html' => $html
                );
            }
        }
        return $ret;
    }
}
