<?php
class Kwc_Articles_Detail_Tags_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasFulltext'] = true;
        $ret['assets']['dep'][] = 'KwfClearOnFocus';
        $ret['assets']['files'][] = 'kwf/Kwc/Articles/Detail/Tags/Component.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $article = $this->getData()->parent->row;
        $s = new Kwf_Model_Select();
        $s->whereEquals('tag_type', 'tag');
        $ret['tags'] = array();
        foreach ($article->getChildRows('ArticleToTag', $s) as $tag) {
            $ret['tags'][] = $tag->tag_name;
        }
        $ret['config'] = array(
            'componentId' => $this->getData()->componentId,
            'controllerUrl' => Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl('SuggestTag')
        );
        return $ret;
    }

    public function getFulltextContent()
    {
        $ret = array();
        $article = $this->getData()->parent->row;
        $s = new Kwf_Model_Select();
        $s->whereEquals('tag_type', 'tag');
        $tags = array();
        foreach ($article->getChildRows('ArticleToTag', $s) as $tag) {
            $tags[] = $tag->tag_name;
        }
        //TODO: better separator that also understands solr?
        $ret['keywords'] = implode(' ', $tags);
        return $ret;
    }
}
