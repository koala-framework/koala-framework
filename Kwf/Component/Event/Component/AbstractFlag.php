<?php
class Kwf_Component_Event_Component_AbstractFlag extends Kwf_Component_Event_Component_Abstract
{
    public $flag;

    /**
     * row got added or removed
     *
     * even if removed the component is still in the component tree (and will be found by getComponentsByDbId)
     */
    const FLAG_ROW_ADDED_REMOVED = 'rowAddedRemoved';

    /**
     * visiblity of row got changed
     *
     * the component tree is updated correctly, use ignoreVisible to search for an changed-to-invisible component
     */
    const FLAG_VISIBILITY_CHANGED = 'visibilityChanged';

    public function __construct($componentClass, $dbId, $flag)
    {
        parent::__construct($componentClass, $dbId);
        $this->flag = $flag;
    }
}
