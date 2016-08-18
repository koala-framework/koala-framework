<?php
class Kwc_Paragraphs_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);

        //don't allow endless recursion
        if (isset($ret['generators']['paragraphs'])) {
            foreach ($ret['generators']['paragraphs']['component'] as $k=>$i) {
                if (is_instance_of($i, 'Kwc_Chained_Cc_Component')
                    && is_instance_of(substr($i, strpos($i, '.')+1), 'Kwc_Chained_CopyTarget_Component')
                ) {
                    unset($ret['generators']['paragraphs']['component'][$k]);
                }
            }
        }
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
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
