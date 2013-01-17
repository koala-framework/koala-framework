<?php
/**
 * Component Plugins that implement this interface can skip the processInput call.
 *
 * gets called also for cached contents
 */
interface Kwf_Component_Plugin_Interface_SkipProcessInput
{
    /**
     * Return true to skip processInput for component and it's children
     */
    public function skipProcessInput();
}
