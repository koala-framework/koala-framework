<?php
class Kwc_Basic_Text_Trl_Admin extends Kwc_Chained_Trl_MasterAsChild_Admin
{
    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $returnMasterContent = false;
        $child = $cmp->getChildComponent('-child');
        if (!$child->hasContent()) {
            $returnMasterContent = true;
        } else {
            $defaultText = Kwc_Abstract::getSetting($child->componentClass, 'defaultText');
            $childRet = \Kwc_Abstract_Admin::getInstance($child->componentClass)->exportContent($child);
            if (isset($childRet['content']) && strip_tags($childRet['content']) == strip_tags($defaultText)) {
                $returnMasterContent = true;
            }
        }

        if ($returnMasterContent) {
            $masterCmp = $cmp->chained;
            $ret = \Kwc_Abstract_Admin::getInstance($masterCmp->componentClass)->exportContent($masterCmp);
        }
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $child = $cmp->getChildComponent('-child');
        $childRow = $child->getComponent()->getRow();
        if (isset($data['content'])) {
            $childRow->content = $data['content'];
        }
        $childRow->save();
    }
}
