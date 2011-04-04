<?php
class Vps_Util_Fulltext
{
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();
            $analyzer->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_ShortWords(2));
            //$stopWords = explode(' ', 'der dir das einer eine ein und oder doch ist sind an in vor nicht wir ihr sie es ich');
            //$analyzer->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_StopWords($stopWords));
            Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);
            Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
            Zend_Search_Lucene_Storage_Directory_Filesystem::setDefaultFilePermissions(0666);
            $path = 'application/cache/fulltext';
            try {
                $instance = Zend_Search_Lucene::open($path);
            } catch (Zend_Search_Lucene_Exception $e) {
                $instance = Zend_Search_Lucene::create($path);
            }
        }
        return $instance;
    }
}
