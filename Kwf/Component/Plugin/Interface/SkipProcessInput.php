<?php
/**
 * Component Plugins that implement this interface can skip the processInput call.
 *
 * gets called also for cached contents
 */
interface Kwf_Component_Plugin_Interface_SkipProcessInput
{
    const SKIP_NONE = false;
    const SKIP_SELF = 'current';
    const SKIP_SELF_AND_CHILDREN = true;

    /**
     * Return constant to choose whicht processInput should be skipped
     */
    public function skipProcessInput();
}
