<?php
abstract class Vpc_TreeCache_StaticPage extends Vpc_TreeCache_Static
{
    protected $_idSeparator = '_';
    protected $_pageDataClass = 'Vps_Component_Data_Page';
    
    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_classes[$componentKey];
        
        $data = parent::_formatConfig($parentData, $componentKey);
        $data['url'] = $parentData->getUrl() . '/' . (isset($c['filename']) ? $c['filename'] : $componentKey); // TODO: reicht noch nicht
        $data['rel'] = isset($c['rel']) ? $c['rel'] : '';
        $data['name'] = isset($c['name']) ? $c['name'] : $componentKey;
        $data['showInMenu'] = !isset($c['showInMenu']) || $c['showInMenu'];
        return $data;
    }
}
