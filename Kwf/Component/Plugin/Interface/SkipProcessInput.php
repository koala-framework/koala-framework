<?php
/**
 * Component Plugins that implement this interface can skip the processInput call.
 *
 * gets called also for cached contents
 */
interface Kwf_Component_Plugin_Interface_SkipProcessInput
{
    /**
     * Return constant to choose whicht processInput should be skipped
     */
    public function skipProcessInput(Kwf_Component_Data $data);
}
