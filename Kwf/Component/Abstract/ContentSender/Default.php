<?php
class Kwf_Component_Abstract_ContentSender_Default extends Kwf_Component_Abstract_ContentSender_Abstract
{
    private function _getRequestWithFiles()
    {
        $ret = $_REQUEST;
        //in _REQUEST sind _FILES nicht mit drinnen
        foreach ($_FILES as $k=>$file) {
            if (is_array($file['tmp_name'])) {
                //wenn name[0] dann kommts in komischer form daher -> umwandeln
                foreach (array_keys($file['tmp_name']) as $i) {
                    foreach (array_keys($file) as $prop) {
                        $ret[$k][$i][$prop] = $file[$prop][$i];
                    }
                }
            } else {
                $ret[$k] = $file;
            }
        }
        return $ret;
    }

    protected function _callProcessInput()
    {
        $showInvisible = Kwf_Config::getValue('showInvisible');

        $cacheId = 'procI-'.$this->_data->getPageOrRoot()->componentId;
        $success = false;
        if (!$showInvisible) { //don't cache in preview
            $processCached = Kwf_Cache_Simple::fetch($cacheId, $success);
            //cache is cleared in Kwf_Component_Events_ProcessInputCache
        }
        if (!$success) {
            $process = $this->_data
                ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true)
                    ));
            if (Kwf_Component_Abstract::getFlag($this->_data->componentClass, 'processInput')) {
                $process[] = $this->_data;
            }

            // TODO: Äußerst suboptimal
            if (is_instance_of($this->_data->componentClass, 'Kwc_Show_Component')) {
                $process += $this->_data->getComponent()->getShowComponent()
                    ->getRecursiveChildComponents(array(
                        'page' => false,
                        'flags' => array('processInput' => true)
                    ));
                if (Kwf_Component_Abstract::getFlag(get_class($this->_data->getComponent()->getShowComponent()->getComponent()), 'processInput')) {
                    $process[] = $this->_data;
                }
            }
            if (!$showInvisible) {
                $datas = array();
                foreach ($process as $p) {
                    $datas[] = $p->kwfSerialize();
                }
                Kwf_Cache_Simple::add($cacheId, $datas);
            }
        } else {
            $process = array();
            foreach ($processCached as $d) {
                $process[] = Kwf_Component_Data::kwfUnserialize($d);
            }
        }

        $postData = $this->_getRequestWithFiles();
        foreach ($process as $i) {
            Kwf_Benchmark::count('processInput', $i->componentId);
            if (method_exists($i->getComponent(), 'preProcessInput')) {
                $i->getComponent()->preProcessInput($postData);
            }
        }
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'processInput')) {
                $i->getComponent()->processInput($postData);
            }
        }
        if (class_exists('Kwf_Component_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Kwf_Component_ModelObserver::getInstance()->process(false);
        }
        return $process;
    }

    protected function _callPostProcessInput($process)
    {
        $postData = $this->_getRequestWithFiles();
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'postProcessInput')) {
                $i->getComponent()->postProcessInput($postData);
            }
        }
        if (class_exists('Kwf_Component_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Kwf_Component_ModelObserver::getInstance()->process();
        }
    }

    public function sendContent($includeMaster = true)
    {
        header('Content-Type: text/html; charset=utf-8');
        $process = $this->_callProcessInput();
        Kwf_Benchmark::checkpoint('processInput');
        echo $this->_data->render(null, $includeMaster);
        Kwf_Benchmark::checkpoint('render');
        $this->_callPostProcessInput($process);
        Kwf_Benchmark::checkpoint('postProcessInput');

    }

}
