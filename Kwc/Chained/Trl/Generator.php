<?php
class Kwc_Chained_Trl_Generator extends Kwc_Chained_Abstract_Generator
{
    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        if (is_instance_of($this->_class, 'Kwc_Root_TrlRoot_Chained_Component')) {
            $ret['trlBase'] = true;
        }
        $ret['chainedType'] = 'Trl';
        return $ret;
    }

    protected function _getChainedSelect($select)
    {
        $select = parent::_getChainedSelect($select);
        $select->ignoreVisible(); // Visible ist bei Trl immer extra zu setzen

        if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
            $select->unsetPart(Kwf_Model_Select::LIMIT_COUNT);
        }
        if ($select->hasPart(Kwf_Model_Select::LIMIT_OFFSET)) {
            $select->unsetPart(Kwf_Model_Select::LIMIT_OFFSET);
        }

        if ($this->_getChainedGenerator() instanceof Kwf_Component_Generator_PseudoPage_Static) {
            //filename is translated, unset, checked in _createData
            $select->unsetPart(Kwf_Component_Select::WHERE_FILENAME);
        }
        return $select;
    }

    protected function _createData($parentData, $row, $select)
    {
        $ret = parent::_createData($parentData, $row, $select);
        if ($this->_getChainedGenerator() instanceof Kwf_Component_Generator_PseudoPage_Static) {
            if ($whereFileName = $select->getPart(Kwf_Component_Select::WHERE_FILENAME)) {
                if ($ret->filename != $whereFileName) {
                    $ret = null;
                }
            }
        }
        return $ret;
    }

    public function getEventsClass()
    {
        if ($this->_eventsClass) return $this->_eventsClass;
        $g = $this->_getChainedGenerator();
        if        ($g instanceof Kwf_Component_Generator_Page_Static) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Static_Page';
        } else if ($g instanceof Kwf_Component_Generator_PseudoPage_Static) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Static_PseudoPage';
        } else if ($g instanceof Kwf_Component_Generator_MultiBox_Static) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Static_MultiBox';
        } else if ($g instanceof Kwf_Component_Generator_Box_Static) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Static_Box';
        } else if ($g instanceof Kwf_Component_Generator_Box_StaticSelect) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Static_BoxSelect';
        } else if ($g instanceof Kwf_Component_Generator_Static) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Static';
        } else if ($g instanceof Kwf_Component_Generator_Page_Table) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Table_Page';
        } else if ($g instanceof Kwf_Component_Generator_PseudoPage_Table) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Table_PseudoPage';
        } else if ($g instanceof Kwf_Component_Generator_Table) {
            return 'Kwc_Chained_Trl_GeneratorEvents_Table';
        }
        return null;
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        if ($this->_getChainedGenerator() instanceof Kwf_Component_Generator_PseudoPage_Static) {
            //get trlStatic setting from chained generator and execute trlStaticExecute again
            $c = $this->_getChainedGenerator()->_settings;
            if (isset($ret['name']) && isset($c['name'])) {
                $ret['name'] = $parentData->trlStaticExecute($c['name']);
            }
            if (isset($ret['filename'])) {
                if (isset($c['filename']) && $c['filename']) {
                    $ret['filename'] = $c['filename'];
                } else if (isset($c['name']) && $c['name']) {
                    $ret['filename'] = $c['name'];
                }
                $ret['filename'] = $parentData->trlStaticExecute($ret['filename']);
                $ret['filename'] = Kwf_Filter::filterStatic($ret['filename'], 'Ascii');
            }
        }
        return $ret;
    }
}
