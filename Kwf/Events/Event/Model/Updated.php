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

    /**
     * Model instance
     *
     * @var Kwf_Model_Abstract
     */
    public $model;

    public function __construct(Kwf_Model_Abstract $model, array $ids = null)
    {
        $this->class = $model->getFactoryId();
        $this->model = $model;
        $this->ids = $ids;
    }

    protected function _getVarsStringArray()
    {
        return array(
            $this->class,
            is_null($this->ids) ? 'null' : implode(', ', $this->ids)
        );
    }
}
