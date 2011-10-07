<?php
class Vps_Component_Generator_PseudoPage_Static extends Vps_Component_Generator_Static
{
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
            if ($parentData) {
                $pData = is_array($parentData) ? $parentData[0] : $parentData;
                $ret = $pData->trlStaticExecute($ret);
            }
        }
        if (!$ret) {
            $ret = $componentKey;
        }
        return Vps_Filter::filterStatic($ret, 'Ascii');
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_settings;

        $data = parent::_formatConfig($parentData, $componentKey);
        $data['name'] = isset($c['name']) ? $parentData->trlStaticExecute($c['name']) : $componentKey;
        $data['filename'] = $this->_getFilenameFromRow($componentKey, $parentData);
        $data['rel'] = isset($c['rel']) ? $c['rel'] : '';
        $data['isPseudoPage'] = true;
        return $data;
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['pseudoPage'] = true;
        return $ret;
    }
}
