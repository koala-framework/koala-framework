<?php
/**
 * @package Component
 * @subpackage Event
 */
abstract class Kwf_Component_Event_Component_Abstract extends Kwf_Component_Event_Abstract
{
    public $dbId;

    public function __construct($componentClass, $dbId)
    {
        $this->class = $componentClass;
        $this->dbId = $dbId;
    }

    public function getParentDbId()
    {
        $pos = max(strrpos($this->dbId, '-'), strrpos($this->dbId, '_'));
        return substr($this->dbId, 0, $pos);
    }
}