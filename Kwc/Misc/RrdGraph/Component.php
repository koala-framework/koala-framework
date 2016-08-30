<?php
class Kwc_Misc_RrdGraph_Component extends Kwc_Abstract
    implements Kwf_Media_Output_Interface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['componentName'] = trlKwfStatic('Rrd Graph');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['src'] = false;
        if ($this->_getGraph()) {
            $ret['src'] = Kwf_Media::getUrl(
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
            if (!in_array($rrd, Kwf_Registry::get('config')->rrd->toArray())) {
                throw new Kwf_Exception("Invalid class '$rrd', not defined in config");
            }
            $rrd = new $rrd();
            $graphs = $rrd->getGraphs();
            if (!isset($graphs[$graph[1]])) {
                throw new Kwf_Exception("Invalid graph '$graph[1]'");
            }
            return $graphs[$graph[1]];
        }
        return null;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type != 'default') return null;
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;
        $graph = $component->getComponent()->_getGraph();
        if (!$graph) return null;

        $row = $component->getComponent()->getRow();

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
}
