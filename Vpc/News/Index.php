<?php
/**
 * Newskomponente
 *
 * @package Vpc
 * @subpackage Components
 */
class Vpc_News_Index extends Vpc_Abstract
{
    public function generateHierarchy($filename = '')
    {
        $pages = array();
        foreach($this->getDao()->getTable('Vpc_News_IndexModel')->fetchAll() as $row) {
            if ($filename != '' && $filename != $row->filename) continue;
            $page = $this->createPage('Vpc_News_Details', 0, $row->id);
            $this->getPagecollection()->addTreePage($page, 'Details', $row->title, $this);
        }
        return $pages;
    }

    public function getTemplateVars($mode)
    {
        $ret = parent::getTemplateVars($mode);

        $news = $this->generateHierarchy();
        $ret['news'] = array();
        foreach($news as $filename => $n) {
            $data['title'] = $this->_titles[$filename];
            $data['filename'] = $n->getUrl();
            $ret['news'][] = $data;
        }
        $ret['id'] = $this->getComponentId();
        $ret['template'] = 'News/Aktuelle.html';
        return $ret;
    }

}