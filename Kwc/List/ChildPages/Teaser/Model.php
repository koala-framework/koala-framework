<?php
class Kwc_List_ChildPages_Teaser_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_childpages_teaser';

    public function updatePages($cmp)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('component_id', $cmp->dbId);
        $rows = $this->getRows($s);
        $rowsByTargetPageId = array();
        foreach ($rows as $r) {
            $rowsByTargetPageId[$r->target_page_id] = $r;
        }

        $childPagesComponentSelect = array(
            'ignoreVisible' => true
        );
        $pos = 0;
        foreach ($cmp->getPage()->getChildPages($childPagesComponentSelect) as $childPage) {
            if (is_numeric($childPage->dbId)) {
                $id = $childPage->dbId;
            } else {
                $id = substr(md5($childPage->dbId), 0, 5);
            }
            $pos++;
            if (isset($rowsByTargetPageId[$childPage->dbId])) {
                $row = $rowsByTargetPageId[$childPage->dbId];
                unset($rowsByTargetPageId[$childPage->dbId]);
            } else {
                $row = $this->createRow();
                $row->target_page_id = $childPage->dbId;
                $row->visible = false;
            }
            $row->child_id = $id;
            $row->pos = $pos;
            $row->component_id = $cmp->dbId;
            $row->save();
        }

        foreach ($rowsByTargetPageId as $row) {
            $row->delete();
        }
    }
}
