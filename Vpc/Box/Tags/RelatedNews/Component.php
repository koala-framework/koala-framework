<?php
class Vpc_Box_Tags_RelatedNews_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Box_Tags_RelatedNews_View_Component';
        $ret['useDirectorySelect'] = false;
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return 'Vpc_News_Directory_Component';
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $plugin = $this->getData()->getPage()->generator->getGeneratorPlugin('tags');
        if (!$plugin) return null;

        $tagIds = array();
        foreach ($plugin->getTags($this->getData()->getPage()) as $tag) {
            $tagIds[] = (int)$tag->id;
        }
        if (!$tagIds) return null;

        $ret->join('vpc_components_to_tags', "vpc_components_to_tags.component_id = CONCAT('news_', vpc_news.id)", array());
        $ret->where('vpc_components_to_tags.tag_id IN ('.implode(',', $tagIds).')');

        return $ret;
    }
}
