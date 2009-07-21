<?php
interface Vps_Component_Plugin_Interface_View
{
    const EXECUTE_BEFORE = 'before';
    const EXECUTE_AFTER = 'after';

    public function processOutput($output);
    public function getExecutionPoint();
}
