<?php
/**
 * @package Component
 * @subpackage Event
 */
class Kwf_Events_Event_Model_Updated extends Kwf_Events_Event_Abstract
{
    /**
     * Ids that changed, can be null if unknown
     *
     * @var array
     */
    public $ids;

    public function __construct(Kwf_Model_Abstract $model, array $ids = null)
    {
        $this->class = $model->getFactoryId();
        $this->ids = $ids;
    }
}
