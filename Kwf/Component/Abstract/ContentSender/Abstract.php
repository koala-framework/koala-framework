<?php
abstract class Kwf_Component_Abstract_ContentSender_Abstract
{
    /**
     * @var Kwf_Component_Data
     */
    protected $_data;
    public function __construct(Kwf_Component_Data $data)
    {
        $this->_data = $data;
    }

    /**
     * returned attributes will be added to link for component that uses this ContentSender
     *
     * used for Lightbox
     */
    public function getLinkDataAttributes()
    {
        return array();
    }

    public function getLinkClass()
    {
        return '';
    }

    abstract public function sendContent($includeMaster);

    protected function _render($includeMaster, &$hasDynamicParts)
    {
        return $this->_data->render(null, $includeMaster, $hasDynamicParts);
    }

    protected function _getProcessInputComponents($includeMaster)
    {
        return self::__getProcessInputComponents($this->_data);
    }

    //public for unittest
    public static function __getProcessInputComponents($data)
    {
        $showInvisible = Kwf_Component_Data_Root::getShowInvisible();

        $cacheId = 'procI-'.$data->componentId;
        $success = false;
        if (!$showInvisible) { //don't cache in preview
            $cacheContents = Kwf_Cache_Simple::fetch($cacheId, $success);
            //cache is cleared in Kwf_Component_Events_ProcessInputCache
        }
        if (!$success) {
            $datas = array();
            foreach (self::_findProcessInputComponents($data) as $p) {
                $plugins = array();
                $c = $p;
                do {
                    foreach ($c->getPlugins('Kwf_Component_Plugin_Interface_SkipProcessInput') as $i) {
                        $plugins[] = array(
                            'pluginClass' => $i,
                            'componentId' => $c->componentId
                        );
                    }
                    $isPage = $c->isPage;
                    $c = $c->parent;
                } while ($c && !$isPage);
                $datas[] = array(
                    'data' => $p,
                    'plugins' => $plugins,
                );
            }
            if (!$showInvisible) {
                $cacheContents = array();
                foreach ($datas as $p) {
                    $cacheContents[] = array(
                        'data' => $p['data']->kwfSerialize(),
                        'plugins' => $p['plugins'],
                    );
                }
                Kwf_Cache_Simple::add($cacheId, $cacheContents);
            }
        } else {
            $datas = array();
            foreach ($cacheContents as $d) {
                $datas[] = array(
                    'data' => Kwf_Component_Data::kwfUnserialize($d['data']),
                    'plugins' => $d['plugins'],
                );
            }
        }
        //ask SkipProcessInput plugins if it should be skipped
        //evaluated every time
        $process = array();
        foreach ($datas as $d) {
            foreach ($d['plugins'] as $p) {
                $plugin = Kwf_Component_Plugin_Abstract::getInstance($p['pluginClass'], $p['componentId']);
                if ($plugin->skipProcessInput($d['data'])) {
                    continue 2;
                }
            }
            $process[] = $d['data'];
        }

        return $process;
    }

    protected static function _findProcessInputComponents($data)
    {
        $process = $data
            ->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('processInput' => true)
                ));
        $process = array_merge($process, $data
            ->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('forwardProcessInput' => true)
                )));
        if (Kwf_Component_Abstract::getFlag($data->componentClass, 'processInput')) {
            $process[] = $data;
        }
        if (Kwf_Component_Abstract::getFlag($data->componentClass, 'forwardProcessInput')) {
            $process[] = $data;
        }
        $ret = array();
        foreach ($process as $i) {
            if (Kwf_Component_Abstract::getFlag($i->componentClass, 'processInput')) {
                $ret[] = $i;
            }
            if (Kwf_Component_Abstract::getFlag($i->componentClass, 'forwardProcessInput')) {
                $ret = array_merge($ret, $i->getComponent()->getForwardProcessInputComponents());
            }
        }
        return $ret;
    }

}
