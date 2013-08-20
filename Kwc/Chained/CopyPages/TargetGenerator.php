<?php
class Kwc_Chained_CopyPages_TargetGenerator extends Kwf_Component_Generator_Static
{
    protected function _idMatches($id)
    {
        return $id == $this->getIdSeparator().$this->getGeneratorKey();
    }

    protected function _fetchKeys($parentData, $select)
    {
        //es gibt exakt eine unterkomponente mit der id 'target'
        $ret = array();
        $select = $this->_formatSelect($parentData, $select);
        if (is_null($select)) return array();
        $ret[] = 'target';
        return $ret;
    }

    protected function _acceptKey($key, $select, $parentData)
    {
        return true;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $target = $parentData->getComponent()->getTargetComponent();
        $componentClass = $this->_settings['masterComponentsMap'][$target->componentClass];
        return array(
            'componentId' => $parentData->componentId . $this->_idSeparator . $componentKey,
            'dbId' => $parentData->dbId . $this->_idSeparator . $componentKey,
            'chained' => $target,
            'componentClass' => $componentClass,
            'parent' => $parentData,
            'isPage' => false,
            'isPseudoPage' => false
        );
    }

    public function getStaticChildComponentIds()
    {
        return array($this->getIdSeparator().$this->getGeneratorKey());
    }
}
