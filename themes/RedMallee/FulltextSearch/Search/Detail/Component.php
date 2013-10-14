<?php
class RedMallee_FulltextSearch_Search_Detail_Component extends Kwc_FulltextSearch_Search_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $title = array();
        $row = $ret['data']->row->data->getPage();
        if (!$row) {
            return null;
        }
        do {
            if ($row->name != '') {
                $title[] = $row->name;
            }
        } while ($row = $row->getParentPage());
        $pagePath = array_reverse($title);
        $ret['pagePath'] = implode(' » ', $pagePath);
        return $ret;
    }
}
