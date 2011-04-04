<?php
class Vpc_Misc_RrdGraph_Component extends Vpc_Abstract
    implements Vps_Media_Output_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['componentName'] = trlVps('Rrd Graph');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['src'] = false;
        if ($this->_getGraph()) {
            $ret['src'] = Vps_Media::getUrl(
                            get_class($this),
                            $this->getData()->componentId,
                            'default',
                            'graph.png'
                        );
        }
        return $ret;
    }

    private function _getGraph()
    {
        $row = $this->getRow();
        $graph = explode(':', $row->graph);
        if (count($graph) == 2) {
            $rrd = $graph[0];
            if (!in_array($rrd, Vps_Registry::get('config')->rrd->toArray())) {
                throw new Vps_Exception("Invalid class '$rrd', not defined in config");
            }
            $rrd = new $rrd();
            $graphs = $rrd->getGraphs();
            if (!isset($graphs[$graph[1]])) {
                throw new Vps_Exception("Invalid graph '$graph[1]'");
            }
            return $graphs[$graph[1]];
        }
        return null;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type != 'default') return null;
        $component = Vps_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;
        $graph = $component->getComponent()->_getGraph();
        if (!$graph) return null;

        $row = $component->getComponent()->getRow();
        Vps_Component_Cache::getInstance()->saveMeta(
            $component,
            new Vps_Component_Cache_Meta_Static_Callback($row->getModel())
        );

        $start = strtotime('-'.$row->duration.' days');
        $output = $graph->getContents(array(
            'start' => $start,
            'width' => $row->width,
            'height' => $row->height
        ));
        return array(
            'lifetime' => $row->cache_lifetime*60,
            'contents' => $output,
            'mimeType' => 'image/png',
            'mtime' => time()
        );
    }

    public function onCacheCallback($row)
    {
        $cacheId = Vps_Media::createCacheId(
            $this->getData()->componentClass, $this->getData()->componentId, 'default'
        );
        Vps_Media::getOutputCache()->remove($cacheId);
    }
}
