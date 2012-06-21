<?php
class Kwf_Component_Cache_Fnf_UrlModel extends Kwf_Component_Cache_Mysql_UrlModel
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'url',
            'columns' => array('url', 'page_id', 'expanded_page_id'),
            'uniqueColumns' => array('url')
        ));
        parent::__construct($config);
    }
}
