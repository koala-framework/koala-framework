<?php
/**
 * Component Plugins that implement this interface can skip the processInput call.
 */
interface Kwf_Component_Plugin_Interface_SkipProcessInput
{
    /**
     * Return true to skip processInput for component and it's children
     */
    public function skipProcessInput();
}
