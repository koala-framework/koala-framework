<?php
class Kwc_Articles_Detail_Tags_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            if (in_array('Kwc_Articles_Directory_Component', Kwc_Abstract::getParentClasses($class))) {
                $childModel = Kwc_Abstract::getSetting($class, 'childModel');
                $articleToTagModel = get_class(Kwf_Model_Abstract::getInstance($childModel)->getDependentModel('ArticleToTag'));
                $ret[] = array(
                    'class' => $articleToTagModel,
                    'event' => 'Kwf_Component_Event_Row_Inserted',
                    'callback' => 'onTagRowUpdate'
                );
                $ret[] = array(
                    'class' => $articleToTagModel,
                    'event' => 'Kwf_Component_Event_Row_Deleted',
                    'callback' => 'onTagRowUpdate'
                );
            }
        }
        return $ret;
    }

    public function onTagRowUpdate(Kwf_Component_Event_Row_Abstract $ev)
    {
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_Articles_Detail_Component', array('id' => $ev->row->article_id));
        foreach ($components as $article) {
            $this->fireEvent(
                new Kwf_Component_Event_Component_ContentChanged($this->_class, $article->getChildComponent('-tags'))
            );
        }
    }
}
