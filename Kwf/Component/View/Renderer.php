<?php
abstract class Kwf_Component_View_Renderer extends Kwf_Component_View_Helper_Abstract
{
    protected function _canBeIncludedInFullPageCache($componentId, $viewCacheEnabled)
    {
        //is caching possible for this type? and is view cache enabled?
        $settings = $this->getViewCacheSettings($componentId);
        return $settings['enabled'] && $viewCacheEnabled;
    }

    protected function _getRenderPlaceholder($componentId, $config = array(), $value = null, $viewCacheEnabled = true)
    {
        $type = $this->_getType();

        $this->_getRenderer()->includedComponent($componentId, $type);

        if ($this->_canBeIncludedInFullPageCache($componentId, $viewCacheEnabled)) {
            $pass = 1;
        } else {
            $pass = 2;
        }
        $config = $config ? base64_encode(serialize($config)) : '';
        return "<kwc$pass $type $componentId $value $config>";
    }

    protected function _getComponentById($componentId)
    {
        $ret = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => true));
        if (!$ret) throw new Kwf_Exception("Can't find component '$componentId' for rendering");
        return $ret;
    }

    protected function _getType()
    {
        $ret = substr(strrchr(get_class($this), '_'), 1);
        $ret = strtolower(substr($ret, 0, 1)).substr($ret, 1); //anfangsbuchstaben klein
        return $ret;
    }

    /**
     * wird für ungecachte komponenten aufgerufen
     *
     * wird nur aufgerufen wenn ungecached
     */
    public abstract function render($componentId, $config);

    /**
     * Kann die render ausgabe (die aus cache oder direkt aus render kommen kann)
     * anpassen.
     *
     * wird immer aufgerufen, auch wenn sie gecached ist
     */
    public function renderCached($cachedContent, $componentId, $config)
    {
        return $cachedContent;
    }

    public function getViewCacheSettings($componentId)
    {
        return array(
            'enabled' => true,
            'lifetime' => null
        );
    }

    public static function replaceHtmlKwfUp($ret)
    {
        $ret = preg_replace_callback('#((class|id|for|style)="[^"]*)"#', array('Kwf_Component_View_Renderer', '_replaceKwfUpCb'), $ret);
        return $ret;
    }

    public static function _replaceKwfUpCb($m)
    {
        return self::_replaceKwfUp($m[0]);
    }

    protected static function _replaceKwfUp($ret)
    {
        static $up;
        if (!isset($up)) {
            $up = Kwf_Config::getValue('application.uniquePrefix');
            if ($up) $up .= '-';
        }
        return str_replace('kwfUp-', $up, $ret);
    }
}
