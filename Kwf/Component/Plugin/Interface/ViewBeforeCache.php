<?php
/**
 * execute before saving to view cache
 */
interface Kwf_Component_Plugin_Interface_ViewBeforeCache
{
    public function processOutput($output);
}
