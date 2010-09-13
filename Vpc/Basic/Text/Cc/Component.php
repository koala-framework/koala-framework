<?php
class Vpc_Basic_Text_Cc_Component extends Vpc_Chained_Cc_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasFulltext'] = true;
        return $ret;
    }

    public function modifyFulltextDocument(Zend_Search_Lucene_Document $doc)
    {
        return $this->getData()->chained->getComponent()->modifyFulltextDocument($doc);
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
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
