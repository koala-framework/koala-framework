<?php
/**
 * Plugin Interface that can process the view output *after* all child components are rendered
 *
 * gets called also for cached contents
 */
interface Kwf_Component_Plugin_Interface_ViewAfterChildRender
{
    public function processOutput($output, $renderer);
}
