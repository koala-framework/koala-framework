<?php
class Vps_Component_Cache_Fnf extends Vps_Component_Cache_Mysql
{
    public function __construct()
    {
        $this->_models = array (
            'cache' => new Vps_Component_Cache_Fnf_Model(),
            'preload' => new Vps_Component_Cache_Fnf_PreloadModel(),
            'metaModel' => new Vps_Component_Cache_Fnf_MetaModelModel(),
            'metaRow' => new Vps_Component_Cache_Fnf_MetaRowModel(),
            'metaCallback' => new Vps_Component_Cache_Fnf_MetaCallbackModel(),
            'metaComponent' => new Vps_Component_Cache_Fnf_MetaComponentModel()
        );
    }
}
