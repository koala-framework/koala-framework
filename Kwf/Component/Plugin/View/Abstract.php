<?php
abstract class Kwf_Component_Plugin_View_Abstract extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_View
{
    public static function getExecutionPoint()
    {
        return Kwf_Component_Plugin_Interface_View::EXECUTE_BEFORE;
    }
}
