<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Events_Event_Model_Updated extends Kwf_Events_Event_Abstract
{
    public function __construct(Kwf_Model_Abstract $model)
    {
        $this->class = get_class($model);
    }
}