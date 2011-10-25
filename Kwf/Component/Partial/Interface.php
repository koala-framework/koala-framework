<?php
interface Kwf_Component_Partial_Interface
{
    public function getPartialVars($partial, $nr, $info);
    public static function getPartialClass($componentClass);
}
