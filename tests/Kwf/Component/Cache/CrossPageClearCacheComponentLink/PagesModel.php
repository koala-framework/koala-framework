<?php
class Kwf_Component_Cache_CrossPageClearCacheComponentLink_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'test1', 'filename' => 'test1', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
            )
        ));
        parent::__construct($config);
    }
}
