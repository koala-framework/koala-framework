<?php
class Kwc_Basic_Headline_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->getComponent()->getRow()->headline1;
    }


    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $ownRow = $cmp->getComponent()->getRow();
        $ret['headline1'] = $ownRow->headline1;
        $ret['headline2'] = $ownRow->headline2;
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $ownRow = $cmp->getComponent()->getRow();
        if (isset($data['headline1'])) {
            $ownRow->headline1 = $data['headline1'];
        }
        if (isset($data['headline2'])) {
            $ownRow->headline2 = $data['headline2'];
        }
        $ownRow->save();
    }
}
