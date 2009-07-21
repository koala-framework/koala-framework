<?php
abstract class Vps_Component_Plugin_Abstract extends Vps_Component_Abstract
{
    const EXECUTE_BEFORE = 'before';
    const EXECUTE_AFTER = 'after';
    
    protected $_componentId;
    public $type = self::EXECUTE_BEFORE;

    public function __construct($componentId)
    {
        $this->_componentId = $componentId;
        parent::__construct($componentId);
    }
}
