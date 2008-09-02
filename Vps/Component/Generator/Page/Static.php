<?php
class Vps_Component_Generator_Page_Static extends Vps_Component_Generator_Static
    implements Vps_Component_Generator_Page_Interface, Vps_Component_Generator_PseudoPage_Interface
{
    protected $_showInMenu = false;
    protected $_idSeparator = '_';

    protected function _formatSelectFilename(Vps_Component_Select $select)
    {
        return $select;
    }

    protected function _fetchKeys($parentData, $select)
    {
        $select = $this->_formatSelect($parentData, $select);
        if ($select) {
            $select->processed(Vps_Component_Select::WHERE_FILENAME);
        }
        return parent::_fetchKeys($parentData, $select);
    }
    
    protected function _acceptKey($key, $select, $parentData)
    {
        $ret = parent::_acceptKey($key, $select, $parentData);
        if ($ret && $select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Vps_Component_Select::WHERE_FILENAME);
            if ($filename != $this->_getFilenameFromRow($key)) return false;
        }
        return $ret;
    }

    protected function _getFilenameFromRow($componentKey)
    {
        $c = $this->_settings;
        if (isset($c['filename'])) {
            $ret = $c['filename'];
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
        $c = $this->_settings;

        $data = parent::_formatConfig($parentData, $componentKey);
        $data['filename'] = $this->_getFilenameFromRow($componentKey);
        $data['rel'] = isset($c['rel']) ? $c['rel'] : '';
        $data['name'] = isset($c['name']) ? $c['name'] : $componentKey;
        $data['isPage'] = true;
        $data['isPseudoPage'] = true;
        return $data;
    }
}
