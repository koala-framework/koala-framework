<?php
class Kwf_Component_Generator_PseudoPage_Static extends Kwf_Component_Generator_Static
{
    protected function _formatSelectFilename(Kwf_Component_Select $select)
    {
        return $select;
    }

    protected function _acceptKey($key, $select, $parentData)
    {
        $ret = parent::_acceptKey($key, $select, $parentData);
        if ($ret && $select->hasPart(Kwf_Component_Select::WHERE_FILENAME)) {
            $filename = $select->getPart(Kwf_Component_Select::WHERE_FILENAME);
            if ($filename != $this->_getFilenameFromRow($key, $parentData)) return false;
        }
        return $ret;
    }

    protected function _getNameFromRow($componentKey, $parentData)
    {
        if (isset($this->_settings['name'])) {
            $ret = $this->_settings['name'];
            if ($parentData) {
                $pData = is_array($parentData) ? $parentData[0] : $parentData;
                $ret = $pData->trlStaticExecute($ret);
            }
        } else {
            $ret = $componentKey;
        }
        return $ret;
    }

    protected function _getFilenameFromRow($componentKey, $parentData)
    {
        $ret = false;
        if (isset($this->_settings['filename'])) {
            $ret = $this->_settings['filename'];
        }
        if (!$ret) {
            $ret = $this->_getNameFromRow($componentKey, $parentData);
        }
        $ret = Kwf_Filter::filterStatic($ret, 'Ascii');
        return $ret;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_settings;

        $data = parent::_formatConfig($parentData, $componentKey);
        $data['name'] = $this->_getNameFromRow($componentKey, $parentData);
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
