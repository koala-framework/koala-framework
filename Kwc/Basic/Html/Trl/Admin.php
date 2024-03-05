<?php
class Kwc_Basic_Html_Trl_Admin extends Kwc_Admin
{
    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $masterRow = $cmp->chained->getComponent()->getRow();
        $ret['content'] = $masterRow->content;
        $trlRow = $cmp->getComponent()->getRow();
        if ($cmp->hasContent() && strip_tags($trlRow->content) != Kwc_Abstract::LOREM_IPSUM) {
            $ret['content'] = $trlRow->content;
        }
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $ownRow = $cmp->getComponent()->getRow();
        if (isset($data['content'])) {
            $ownRow->content = $data['content'];
        }
        $ownRow->save();
    }
}
