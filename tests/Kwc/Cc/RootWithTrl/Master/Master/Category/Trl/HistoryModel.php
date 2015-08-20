<?php
class Kwc_Cc_RootWithTrl_Master_Master_Category_Trl_HistoryModel extends Kwc_Root_Category_Trl_HistoryModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF();
        parent::__construct($config);
    }
}
