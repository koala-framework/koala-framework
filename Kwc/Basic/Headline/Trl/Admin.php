<?php
class Kwc_Basic_Headline_Trl_Admin extends Kwc_Chained_Abstract_Admin
{
    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $trlRow = $cmp->getComponent()->getRow();
        $masterRow = $cmp->chained->getComponent()->getRow();
        $ret['headline1'] = $trlRow->headline1 ? $trlRow->headline1 : $masterRow->headline1;
        $ret['headline2'] = $trlRow->headline2 ? $trlRow->headline2 : $masterRow->headline2;
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $trlRow = $cmp->getComponent()->getRow();
        if (isset($data['headline1'])) {
            $trlRow->headline1 = $data['headline1'];
        }
        if (isset($data['headline2'])) {
            $trlRow->headline2 = $data['headline2'];
        }
        $trlRow->save();
    }
}
