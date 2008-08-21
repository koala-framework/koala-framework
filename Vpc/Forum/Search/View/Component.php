<?php
class Vpc_Forum_Search_View_Component extends Vpc_Forum_Group_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['searchForm'] = 'Vpc_Forum_Search_View_SearchForm_Component';
        $ret['searchQueryFields'] = 'vpc_posts.content';
        return $ret;
    }
}
