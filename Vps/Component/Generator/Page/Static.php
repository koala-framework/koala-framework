<?php
class Vps_Component_Generator_Page_Static extends Vps_Component_Generator_Static implements Vps_Component_Generator_Page_Interface
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

    protected function _acceptKey($key, $constraints, $parentData)
    {
        $ret = parent::_acceptKey($key, $constraints, $parentData);
        if ($ret) {
            $c = $this->_settings['component'][$key];
            if (isset($constraints['filename']) &&
                $constraints['filename'] != $this->_getFilenameFromRow($key)
            ) {
                $ret = false;
            }
        }
        return $ret;
    }

    protected function _getFilenameFromRow($componentKey)
    {
        $c = $this->_settings['component'][$componentKey];
        if (isset($c['filename'])) {
            return $c['filename'];
        }
        if (isset($c['name'])) {
            $ret = $c['name'];
        } else {
            $ret = $componentKey;
        }
        return Vps_Filter::get($ret, 'Ascii');
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_settings['component'][$componentKey];

        $data = parent::_formatConfig($parentData, $componentKey);
        $data['filename'] = $this->_getFilenameFromRow($componentKey);
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
