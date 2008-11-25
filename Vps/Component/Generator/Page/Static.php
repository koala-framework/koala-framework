<?php
class Vps_Component_Generator_Page_Static extends Vps_Component_Generator_Static
    implements Vps_Component_Generator_Page_Interface, Vps_Component_Generator_PseudoPage_Interface
{
    protected $_idSeparator = '_';

    protected function _formatSelectFilename(Vps_Component_Select $select)
    {
        return $select;
    }

    protected function _acceptKey($key, $select, $parentData)
    {
        $ret = parent::_acceptKey($key, $select, $parentData);
        if ($ret && $select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Vps_Component_Select::WHERE_FILENAME);
            if ($filename != $this->_getFilenameFromRow($key, $parentData)) return false;
        }
        return $ret;
    }

    protected function _getFilenameFromRow($componentKey, $parentData)
    {
        $ret = false;

        $c = $this->_settings;
        if (isset($c['filename'])) {
            $ret = $c['filename'];
        }
        if (!$ret && isset($c['name'])) {
            $ret = $c['name'];
        }
        if (!$ret) {
            $ret = $componentKey;
        }
        return Vps_Filter::get($ret, 'Ascii');
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_settings;

        $data = parent::_formatConfig($parentData, $componentKey);
        $data['filename'] = $this->_getFilenameFromRow($componentKey, $parentData);
        $data['rel'] = isset($c['rel']) ? $c['rel'] : '';
        $data['name'] = isset($c['name']) ? $c['name'] : $componentKey;
        $data['isPage'] = true;
        $data['inherits'] = true;
        $data['isPseudoPage'] = true;
        return $data;
    }
}
