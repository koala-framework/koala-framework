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
        return $select;
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
}