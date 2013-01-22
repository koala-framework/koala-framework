<?php
class Kwc_Articles_Directory_Events extends Kwc_Directories_Item_Directory_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
            $childModel = Kwc_Abstract::getSetting($this->_class, 'childModel');
            $tagModel = Kwf_Model_Abstract::getInstance($childModel)->getDependentModel('ArticleToTag')->getReference('Tag');
            $ret[] = array(
                'class' => $tagModel['refModelClass'],
                'event' => 'Kwf_Component_Event_Row_Updated',
                'callback' => 'onTagRowUpdate'
            );
        return $ret;
    }

    public function onTagRowUpdate(Kwf_Component_Event_Row_Abstract $event)
    {
        $this->fireEvent(new Kwf_Component_Event_ComponentClass_ContentChanged($this->_class));
    }
}
