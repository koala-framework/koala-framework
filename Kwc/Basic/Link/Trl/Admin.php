<?php
class Kwc_Basic_Link_Trl_Admin extends Kwc_Basic_Link_Admin
{
    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $trlRow = $cmp->getComponent()->getRow();
        $masterRow = $cmp->chained->getComponent()->getRow();
        $ret['text'] = $trlRow->text ? $trlRow->text : $masterRow->text;
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $ownRow = $cmp->getComponent()->getRow();
        if (isset($data['text'])) {
            $ownRow->text = $data['text'];
        }
        $ownRow->save();
    }
}
