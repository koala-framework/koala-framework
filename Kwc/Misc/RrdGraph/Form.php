<?php
class Kwc_Misc_RrdGraph_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->_rrds = array();
        foreach (Kwf_Registry::get('config')->rrd as $k=>$n) {
            $this->_rrds[$k] = new $n;
        }
        $this->_rrds = array_reverse($this->_rrds);
        $values = array();
        foreach ($this->_rrds as $rrd) {
            foreach ($rrd->getGraphs() as $k=>$g) {
                $t = $g->getTitle();
                if (!$t) $t = $k;
                $values[get_class($rrd).':'.$k] = $rrd->getTitle().': '.$t;
            }
        }

        $this->add(new Kwf_Form_Field_Select('graph', trlKwf('Graph')))
            ->setValues($values)
            ->setWidth(300);

        $this->add(new Kwf_Form_Field_NumberField('duration', trlKwf('duration (days)')));
        $this->add(new Kwf_Form_Field_NumberField('cache_lifetime', trlKwf('Cache lifetime (minutes)')));

        $this->add(new Kwf_Form_Field_NumberField('width', trlKwf('Width')));
        $this->add(new Kwf_Form_Field_NumberField('height', trlKwf('Height')));
    }
}
