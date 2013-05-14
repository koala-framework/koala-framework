<?php
class Kwc_Paragraphs_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['editComponents'] = array('paragraphs');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $paragraphs = $this->getData()->getChildComponents(array('generator'=>'paragraphs'));

        $paragraphsById = array();
        foreach ($paragraphs as $c) {
            $paragraphsById[$c->id] = $c;
        }

        foreach(array_keys($ret['paragraphs']) as $key) {
            $ret['paragraphs'][$key]['data'] = $paragraphsById[$ret['paragraphs'][$key]['data']->id];
        }
        return $ret;
    }

    public function hasContent()
    {
        $childComponents = $this->getData()->getChildComponents(array('generator' => 'paragraphs'));
        foreach ($childComponents as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }
}
