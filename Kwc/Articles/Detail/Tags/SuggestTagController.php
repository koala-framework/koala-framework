<?php
class Kwc_Articles_Detail_Tags_SuggestTagController extends Kwf_Controller_Action
{
    protected function _isAllowedComponent()
    {
        return !!Kwf_Registry::get('userModel')->getAuthedUser();
    }

    public function jsonSuggestAction()
    {
        $newTag = trim($this->_getParam('tag'));
        $data = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'));
        $article = $data->parent->row;

        $s = new Kwf_Model_Select();
        $s->whereEquals('type', 'tag');
        $s->whereEquals('name', $newTag);
        $tag = Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_TagsModel')->getRow($s);
        if (!$tag) {
            $tag = Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_TagsModel')->createRow();
            $tag->type = 'tag';
            $tag->name = $newTag;
            $tag->save();
        }

        $s = new Kwf_Model_Select();
        $s->whereEquals('tag_id', $tag->id);
        if (!count($article->getChildRows('ArticleToTag', $s))) {
            $articleToTag = $article->createChildRow('ArticleToTag');
            $articleToTag->tag_id = $tag->id;
            $articleToTag->save();

            $r = Kwf_Model_Abstract::getInstance('Kwc_Articles_Directory_TagSuggestionsModel')->createRow();
            $r->article_to_tag_id = $articleToTag->id;
            $r->date = date('Y-m-d H:i:s');
            $r->user_id = Kwf_Registry::get('userModel')->getAuthedUser()->id;
            $r->save();
        }

        $s = new Kwf_Model_Select();
        $s->whereEquals('tag_type', 'tag');
        $this->view->tags = array();
        foreach ($article->getChildRows('ArticleToTag', $s) as $tag) {
            $this->view->tags[] = $tag->tag_name;
        }
        $this->view->tags = implode(', ', $this->view->tags);
    }
}
