<?php
class Kwc_Blog_Detail_Events extends Kwc_Directories_Item_Detail_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => 'Kwc_Blog_Category_Directory_BlogPostsToCategoriesModel',
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onRowInsertDelete',
        );

        $ret[] = array(
            'class' => 'Kwc_Blog_Category_Directory_BlogPostsToCategoriesModel',
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onRowInsertDelete',
        );

        $placeholders = Kwc_Abstract::getSetting($this->_class, 'placeholder');
        if ($placeholders['nextLink'] || $placeholders['previousLink']) {
            $ret[] = array(
                'class' => $this->_class,
                'event' => 'Kwf_Component_Event_Component_Added',
                'callback' => 'onDetailAddedRemoved',
            );
            $ret[] = array(
                'class' => $this->_class,
                'event' => 'Kwf_Component_Event_Component_Removed',
                'callback' => 'onDetailAddedRemoved',
            );
        }

        return $ret;
    }

    public function onRowInsertDelete(Kwf_Component_Event_Row_Abstract $ev)
    {
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass(
            $this->_class, array(
                'id' => $ev->row->blog_post_id
            )
        );
        foreach ($components as $c) {
            $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged($this->_class, $c));
        }
    }
    public function onGeneratorRowUpdate(Kwf_Component_Event_Component_RowUpdated $event)
    {
        parent::onGeneratorRowUpdate($event);

        $placeholders = Kwc_Abstract::getSetting($this->_class, 'placeholder');
        if ($placeholders['nextLink'] || $placeholders['previousLink']) {
            if (in_array('publish_date', $event->component->row->getDirtyColumns())) {
                //next / previous links might change
                $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($event->class));
            }
        }
    }
    public function onDetailAddedRemoved(Kwf_Component_Event_Component_AbstractFlag $event)
    {
        //next / previous links might change
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($event->class));
    }
}
