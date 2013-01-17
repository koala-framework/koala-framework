<?php
/**
 * execute before saving to view cache
 *
 * gets called also for cached contents
 */
interface Kwf_Component_Plugin_Interface_ViewBeforeCache
{
    public function processOutput($output);
}
