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
        return $this->getData()->chained()->getComponent()->modifyFulltextDocument($doc);
    }
}
