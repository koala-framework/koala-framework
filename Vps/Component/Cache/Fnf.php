<?php
class Vps_Component_Cache_Fnf extends Vps_Component_Cache_Mysql
{
    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Vps_Component_Cache_Fnf_Model',
            'preload' => 'Vps_Component_Cache_Fnf_PreloadModel',
            'metaModel' => 'Vps_Component_Cache_Fnf_MetaModelModel',
            'metaRow' => 'Vps_Component_Cache_Fnf_MetaRowModel',
            'metaComponent' => 'Vps_Component_Cache_Fnf_MetaComponentModel',
            'metaChained' => 'Vps_Component_Cache_Fnf_MetaChainedModel',
            'url' => 'Vps_Component_Cache_Fnf_UrlModel',
            'urlParents' => 'Vps_Component_Cache_Fnf_UrlParentsModel',
            'processInput' => 'Vps_Component_Cache_Fnf_ProcessInputModel'
        );
    }

    protected function _preload($where)
    {
        $or = array();

        // Alles von eigener Page
        $or = array();
        foreach ($where as $pageId => $componentIds) {
            if (is_null($componentIds)) {
                $or[] = new Vps_Model_Select_Expr_IsNull('page_id');
            } else {
                foreach ($componentIds as $componentId) {
                    if (strpos($componentId, '%') !== false) {
                        $or[] = new Vps_Model_Select_Expr_And(array(
                            new Vps_Model_Select_Expr_Equal('page_id', $pageId),
                            new Vps_Model_Select_Expr_Like('component_id', $componentId)
                        ));
                    } else {
                        $or[] = new Vps_Model_Select_Expr_Equal('component_id', $componentId);
                    }
                }
            }
        }

        $select = $this->getModel()->select()->where(
            new Vps_Model_Select_Expr_And(array(
                new Vps_Model_Select_Expr_Equal('deleted', 0),
                new Vps_Model_Select_Expr_Or(array(
                    new Vps_Model_Select_Expr_IsNull('expire'),
                    new Vps_Model_Select_Expr_HigherEqual('expire', time())
                )),
                new Vps_Model_Select_Expr_Or($or)
            ))
        );

        return $this->getModel()->export(Vps_Model_Db::FORMAT_ARRAY, $select);
    }
}
