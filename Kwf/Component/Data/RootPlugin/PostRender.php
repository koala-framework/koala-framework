<?php
class Kwf_Component_Data_RootPlugin_PostRender
    implements Kwf_Component_Data_RootPlugin_Interface_PostRender
{
    protected function _unmask($output, $params)
    {
        $params = base64_encode(json_encode($params));
        return preg_replace(
            "#(<!-- postRenderPluginBegin $params )(.*?)( postRenderPluginEnd $params -->)#s",
            '$2',
            $output
        );
    }

    protected function _mask($output, $params)
    {
        $params = base64_encode(json_encode($params));
        return preg_replace(
            "#(<!-- postRenderPluginBegin $params -->)(.*?)(<!-- postRenderPluginEnd $params -->)#s",
            '',
            $output
        );
    }

    public function processOutput($output)
    {
        return $output;
    }
}
