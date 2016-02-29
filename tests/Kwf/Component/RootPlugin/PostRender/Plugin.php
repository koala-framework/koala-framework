<?php
class Kwf_Component_RootPlugin_PostRender_Plugin implements Kwf_Component_Data_RootPlugin_Interface_PostRender
{
    public function processOutput($output)
    {
        return $output . 'bar';
    }
}
