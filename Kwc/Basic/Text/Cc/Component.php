<?php
class Kwc_Basic_Text_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasFulltext'] = true;
        return $ret;
    }

    public function getFulltextContent()
    {
        return $this->getData()->chained->getComponent()->getFulltextContent();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $childs = $this->getData()->getChildComponents();
        foreach ($ret['contentParts'] as $kPart=>$part) {
            if (is_array($part)) {
                $component = self::getChainedByMaster($part['component'], $this->getData(), array(
                    'ignoreVisible' => true
                ));
                $ret['contentParts'][$kPart]['component'] = $component;
            }
        }
        return $ret;
    }
}
