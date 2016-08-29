<?php
class Kwf_Component_Cache_Url_Fnf_Model extends Kwf_Model_FnF
{
    protected $_primaryKey = 'url';
    protected $_columns = array('url', 'page_id', 'expanded_page_id');
    protected $_uniqueColumns = array('url');
}
