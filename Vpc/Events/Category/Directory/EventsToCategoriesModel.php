<?php
class Vpc_Events_Category_Directory_EventsToCategoriesModel
    extends Vpc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_name = 'vpc_events_to_categories';

    protected function _setup()
    {
        $this->_referenceMap['Item'] = array(
            'columns'           => array('event_id'),
            'refTableClass'     => 'Vpc_Events_Directory_Model',
            'refColumns'        => array('id')
        );
        parent::_setup();
    }
}
