<?php
class Kwc_Shop_Products_Detail_AddToCartGenerator extends Kwf_Component_Generator_Static
{
    protected function _getChildComponentClass($key, $parentData)
    {
        if ($key != $this->getGeneratorKey()) {
            throw new Kwf_Exception("invalid key '$key'");
        }
        $generators = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        if (count($generators['addToCart']['component']) <= 1) {
            return $generators['addToCart']['component']['product'];
        }
        if ($parentData) {
            foreach ($generators['addToCart']['component'] as $component => $class) {
                if ($component == $parentData->row->component) {
                    return $class;
                }
            }
        }
        return null;
    }

    public function getStaticChildComponentIds()
    {
        return array(
            $this->getIdSeparator().$this->getGeneratorKey()
        );
        foreach (array_keys($this->getChildComponentClasses()) as $c) {
            $childComponentIds[] = $this->getIdSeparator().$c;
        }
        return $childComponentIds;
    }

    protected function _formatConfig($parentData, $key)
    {
        $ret = array(
            'componentId' => $parentData->componentId . $this->_idSeparator . $this->getGeneratorKey(),
            'dbId' => $parentData->dbId . $this->_idSeparator . $this->getGeneratorKey(),
            'parent' => $parentData,
            'isPage' => false,
            'isPseudoPage' => false,
            'inherit' => false
        );        
        $ret['componentClass'] = $this->_getChildComponentClass($key, $parentData);
        return $ret;
    }

    public function getChildData($parentData, $select = array())
    {
        Kwf_Benchmark::count('GenStaticSelect::getChildData');

        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }
        $data = $this->_createData($parentData, $this->getGeneratorKey(), $select);
        return array($data);
    }
}
