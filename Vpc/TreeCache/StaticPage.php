<?php
abstract class Vpc_TreeCache_StaticPage extends Vpc_TreeCache_Static
{
    protected $_showInMenu = false;
    protected $_idSeparator = '_';

    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['page'])) {
            if (!$constraints['page']) return null;
            unset($constraints['page']);
        }
        $filename = isset($constraints['filename']) ? $constraints['filename'] : null;
        if (isset($constraints['showInMenu'])) {
            $showInMenu = $constraints['showInMenu'];
            if ($constraints['showInMenu'] && !$this->_showInMenu) return null;
            if (!$constraints['showInMenu'] && $this->_showInMenu) return null;
            unset($constraints['showInMenu']);
        }
        $constraints = parent::_formatConstraints($parentData, $constraints);
        if ($filename) { $constraints['filename'] = $filename; }
        if (isset($showInMenu)) { $constraints['showInMenu'] = $showInMenu; }
        return $constraints;
    }

    protected function _acceptKey($key, $constraints)
    {
        $ret = parent::_acceptKey($key, $constraints);
        if ($ret) {
            $c = $this->_classes[$key];
            if (isset($constraints['filename']) &&
                ($constraints['filename'] != (isset($c['filename']) ? $c['filename'] : $key))
            ) {
                $ret = false;
            }
        }
        return $ret;
    }
    
    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_classes[$componentKey];

        $data = parent::_formatConfig($parentData, $componentKey);
        $data['filename'] = isset($c['filename']) ? $c['filename'] : $componentKey; // TODO: reicht noch nicht
        $data['rel'] = isset($c['rel']) ? $c['rel'] : '';
        $data['name'] = isset($c['name']) ? $c['name'] : $componentKey;
        $data['isPage'] = true;
        return $data;
    }

    public function createsPages()
    {
        return true;
    }

}
