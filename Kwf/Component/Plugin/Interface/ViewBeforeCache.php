<?php
/**
 * execute before saving to view cache
 *
 * does not get called for cached contents
 */
interface Kwf_Component_Plugin_Interface_ViewBeforeCache
{
    public function processOutput($output, $renderer);
}
