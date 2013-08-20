<?php
class Kwf_Component_Renderer extends Kwf_Component_Renderer_Abstract
{
    public function renderMaster($component)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        $this->_renderComponent = $component;

        $content = false;
        if ($this->_enableCache) {
            $content = Kwf_Component_Cache::getInstance()->load($component->componentId, 'component', 'fullPage');
            $this->_minLifetime = null;
        }

        Kwf_Benchmark::checkpoint('load fullPage cache');

        $statType = null;
        if (!$content) {
            if ($benchmarkEnabled) $startTime = microtime(true);
            if (!$this->_enableCache ||
                ($content = Kwf_Component_Cache::getInstance()->load($component, 'component', 'page')) === null) {
                $masterHelper = new Kwf_Component_View_Helper_Master();
                $masterHelper->setRenderer($this);
                $content = $masterHelper->master($component);
                if ($this->_enableCache) {
                    Kwf_Component_Cache::getInstance()
                        ->save($component, $content, 'component', 'page');

                    $statType = 'miss';
                } else {
                    $statType = 'noviewcache';
                }
            } else {
                $statType = 'hit';
            }
            if ($statType) Kwf_Benchmark::count("rendered $statType", $component->componentId.': page');
            Kwf_Benchmark::countLog('render-'.$statType);

            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($component->componentId.' page', microtime(true)-$startTime);
            Kwf_Benchmark::checkpoint('render page');

            $content = $this->_render(1, $content);
            Kwf_Benchmark::checkpoint('render pass 1');
            Kwf_Component_Cache::getInstance()->save($component, $content, 'component', 'fullPage', '', $this->_minLifetime);
            Kwf_Benchmark::count("rendered miss", $component->componentId.': fullPage');

            Kwf_Benchmark::countLog('fullpage-miss');
        } else {
            Kwf_Benchmark::count("rendered hit", $component->componentId.': fullPage');
            Kwf_Benchmark::countLog('fullpage-hit');
        }

        $content = $this->_render(2, $content);
        Kwf_Benchmark::checkpoint('render pass 2');



        Kwf_Component_Cache::getInstance()->writeBuffer();
        return $content;
    }

    protected function _getRendererName()
    {
        return 'component';
    }
}
