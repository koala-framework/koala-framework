<?php
class Kwf_Component_PluginRoot_PostRender_Plugin implements Kwf_Component_PluginRoot_Interface_PostRender
{
    public function processOutput($output)
    {
        return $output . 'bar';
    }
}
