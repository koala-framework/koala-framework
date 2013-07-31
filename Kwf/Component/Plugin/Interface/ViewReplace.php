<?php
/**
 * Plugin Interface that can replace the view output with custom output.
 *
 * gets called also for cached contents
 */
interface Kwf_Component_Plugin_Interface_ViewReplace
{
    /**
     * @return string|bool false to use component output, string to replace output
     */
    public function replaceOutput($renderer);
}
