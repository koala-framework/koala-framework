<?php
class Kwc_Tags_Suggestions_Controller extends Kwf_Controller_Action
{
    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }

    public function jsonSuggestAction()
    {
        $newTag = trim($this->_getParam('tag'));
        $componentId = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible' => true))
            ->parent->getDbId();

        $newTags = explode(',', $newTag);
        foreach ($newTags as $newTag) {
            // To remove possible xss security problem
            $newTag = trim($newTag);
            $newTag = Kwf_Util_HtmlSpecialChars::filter($newTag);

            $select = new Kwf_Model_Select();
            $select->whereEquals('name', $newTag);

            $tagsModel = Kwf_Model_Abstract::getInstance('Kwc_Tags_Model');
            $tag = $tagsModel->getRow($select);
            if (!$tag) {
                $tag = $tagsModel->createRow();
                $tag->name = $newTag;
                $tag->save();
            }

            $componentToTagModel = Kwf_Model_Abstract::getInstance('Kwc_Tags_ComponentToTag');
            $select = new Kwf_Model_Select();
            $select->whereEquals('component_id', $componentId);
            $select->whereEquals('tag_id', $tag->id);
            if (!$componentToTagModel->countRows($select)) {
                $componentToTag = $componentToTagModel->createRow();
                $componentToTag->component_id = $componentId;
                $componentToTag->tag_id = $tag->id;
                $componentToTag->save();

                $r = Kwf_Model_Abstract::getInstance('Kwc_Tags_Suggestions_Model')->createRow();
                $r->tags_to_components_id = $componentToTag->id;
                $r->date = date('Y-m-d H:i:s');
                $r->user_id = Kwf_Registry::get('userModel')->getAuthedUser()->id;
                $r->save();
            }
        }

        $select = new Kwf_Model_Select();
        $select->whereEquals('component_id', $componentId);
        $this->view->tags = array();
        foreach ($componentToTagModel->getRows($select) as $tag) {
            if ($tag->countChildRows('Suggestions') > 0) {
                $suggestion = $tag->getChildRows('Suggestions')->current();
                if ($suggestion->status != 'accepted') continue;
            }

            $this->view->tags[] = $tag->tag_name;
        }
        $this->view->tags = implode(', ', $this->view->tags);
    }
}
