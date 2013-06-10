<?php
class Kwc_Blog_Detail_Events extends Kwc_Abstract_Events
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
}
