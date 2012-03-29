<?php
class Kwf_Util_Fulltext_Lucene
{
    /**
     * Returns Zend_Search_Lucene instance for given subroot
     *
     * every subroot has it's own instance
     *
     * @param Kwf_Component_Data for this index
     * @return Zend_Search_Lucene_Interface
     */
    public static function getInstance(Kwf_Component_Data $subroot)
    {
        while ($subroot) {
            if (Kwc_Abstract::getFlag($subroot->componentClass, 'subroot')) {
                break;
            }
            $subroot = $subroot->parent;
        }
        if (!$subroot) Kwf_Component_Data_Root::getInstance();

        static $instance = array();
        if (!isset($instance[$subroot->componentId])) {
            $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();
            $analyzer->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_ShortWords(2));
            //$stopWords = explode(' ', 'der dir das einer eine ein und oder doch ist sind an in vor nicht wir ihr sie es ich');
            //$analyzer->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_StopWords($stopWords));
            Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);
            Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
            Zend_Search_Lucene_Storage_Directory_Filesystem::setDefaultFilePermissions(0666);
            $path = 'cache/fulltext';
            $path .= '/'.$subroot->componentId;
            try {
                $instance[$subroot->componentId] = Zend_Search_Lucene::open($path);
            } catch (Zend_Search_Lucene_Exception $e) {
                $instance[$subroot->componentId] = Zend_Search_Lucene::create($path);
            }
        }
        return $instance[$subroot->componentId];
    }

    public static function getInstances()
    {
        $ret = array();
        foreach (new DirectoryIterator('cache/fulltext') as $i) {
            if ($i->isDir() && !$i->isDot()) {
                $ret[$i->getFilename()] = Zend_Search_Lucene::open('cache/fulltext/'.$i->getFilename());
            }
        }
        return $ret;
    }
}
