<?php
class Kwf_Component_PagesMetaModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwf_pages_meta';
    protected $_primaryKey = 'page_id';
    protected $_rowClass = 'Kwf_Component_PagesMetaRow';
    private static $_instance;

    /**
     * @return self
     */
    public static function getInstance($modelName = null)
    {
        if ($modelName) throw new Kwf_Exception("modelName parameter must not be set");

        if (isset(self::$_instance)) {
            return self::$_instance;
        }
        return Kwf_Model_Abstract::getInstance('Kwf_Component_PagesMetaModel');
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public static function setInstance($instance)
    {
        self::$_instance = $instance;
    }

    //for tests
    //in web ComponentPagesMetaController is used
    public function indexRecursive(Kwf_Component_Data $page)
    {
        if ($page->isPage) {
            $r = $this->getRow($page->componentId);
            if (!$r) {
                $r = $this->createRow();
                $r->changed_date = date('Y-m-d H:i:s');
            }
            $r->updateFromPage($page);
            $r->save();
        }

        $childPages = $page->getChildPseudoPages(
            array('pageGenerator' => false),
            array('pseudoPage'=>false, 'unique'=>false) //don't recurse into unique boxes, causes endless recursion if box creates page
        );
        $childPages = array_merge($childPages, $page->getChildPseudoPages(
            array('pageGenerator' => true),
            array('pseudoPage'=>false)
        ));
        $ret = array();
        foreach ($childPages as $p) {
            $this->indexRecursive($p);
        }
    }
}
