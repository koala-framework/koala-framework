<?php
class Kwc_Box_Tags_RelatedNews_Component extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwc_Box_Tags_RelatedNews_View_Component';
        $ret['useDirectorySelect'] = false;
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return 'Kwc_News_Directory_Component';
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        return array('Kwc_News_Directory_Component');
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $tagIds = $this->_getTagIds();
        if (!$tagIds) return null;

        $ret->join('kwc_components_to_tags', "kwc_components_to_tags.component_id = CONCAT('news_', kwc_news.id)", array());
        $ret->where('kwc_components_to_tags.tag_id IN ('.implode(',', $tagIds).')');
        $ret->group('kwc_news.id');

        //eigene seite nicht anzeigen
        $ret->where('kwc_components_to_tags.component_id != ?', $this->getData()->getPage()->dbId);

        return $ret;
    }

    protected function _getTagIds()
    {
        if (!$this->getData()->getPage() || !$this->getData()->getPage()->generator) return null;
        $plugin = $this->getData()->getPage()->generator->getGeneratorPlugin('tags');
        if (!$plugin) return null;

        $ret = array();
        foreach ($plugin->getTags($this->getData()->getPage()) as $tag) {
            $ret[] = (int)$tag->id;
        }
        return $ret;
    }
}
