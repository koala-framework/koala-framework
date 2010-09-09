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
                if ($part['type'] == 'image') {
                    $part['nr'] = 'i'.$part['nr'];
                } else if ($part['type'] == 'link') {
                    $part['nr'] = 'l'.$part['nr'];
                } else if ($part['type'] == 'download') {
                    $part['nr'] = 'd'.$part['nr'];
                } else {
                    continue;
                }
                foreach ($childs as $child) {
                    if ($child->dbId == $this->getData()->dbId.'-'.$part['nr']) {
                        $ret['contentParts'][$kPart] = array(
                            'type' => $part['type'],
                            'component'=>$row
                        );
                        break;
                    }
                }
            }
        }
        return $ret;
    }
}
