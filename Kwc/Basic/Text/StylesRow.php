<?php
class Kwc_Basic_Text_StylesRow extends Kwf_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->name;
    }
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->getModel()->removeCache();
    }
    protected function _afterDelete()
    {
        parent::_afterDelete();
        $this->getModel()->removeCache();
    }

    public function getStyles()
    {
        $css = array();
        foreach ($this->getSiblingRow('styles')->toArray() as $name=>$value) {
            if (!$value) continue;
            if ($name == 'id') continue;
            $name = str_replace('_', '-', $name);
            if ($name == 'additional') {
                foreach (explode(';', $value) as $i) {
                    if (preg_match('#^\s*([a-z-]+)\s*:\s*(.*)\s*$#', $i, $m)) {
                        $css[$m[1]] = $m[2];
                    }
                }
                continue;
            } else if ($name == 'margin-top' || $name == 'margin-bottom'
                    || $name=='font-size') {
                $value .= 'px';
            } else if ($name == 'color') {
                $value = '#'.$value;
            }
            $css[$name] = $value;
        }
        return $css;
    }
}
