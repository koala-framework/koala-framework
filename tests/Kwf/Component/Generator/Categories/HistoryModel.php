<?php
class Kwf_Component_Generator_Categories_HistoryModel extends Kwc_Root_Category_HistoryModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF();
        parent::__construct($config);
    }
}
