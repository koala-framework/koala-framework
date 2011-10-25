<?php
class Kwc_Events_Category_Directory_EventsToCategoriesModel
    extends Kwc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_table = 'kwc_events_to_categories';

    protected function _init()
    {
        $this->_referenceMap['Item'] = array(
            'column'           => 'event_id',
            'refModelClass'     => 'Kwc_Events_Directory_Model',
            'refColumns'        => array('id')
        );
        parent::_init();
    }
}
