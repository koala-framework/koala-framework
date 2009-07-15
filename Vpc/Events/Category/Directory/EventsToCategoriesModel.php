<?php
class Vpc_Events_Category_Directory_EventsToCategoriesModel
    extends Vpc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_table = 'vpc_events_to_categories';

    protected function _init()
    {
        $this->_referenceMap['Item'] = array(
            'column'           => 'event_id',
            'refTableClass'     => 'Vpc_Events_Directory_Model',
            'refColumns'        => array('id')
        );
        parent::_init();
    }
}
