<?php
class Vpc_Misc_RrdGraph_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->_rrds = array();
        foreach (Vps_Registry::get('config')->rrd as $k=>$n) {
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

        $this->add(new Vps_Form_Field_Select('graph', trlVps('Graph')))
            ->setValues($values)
            ->setWidth(300);

        $this->add(new Vps_Form_Field_NumberField('duration', trlVps('duration (days)')));
        $this->add(new Vps_Form_Field_NumberField('cache_lifetime', trlVps('Cache lifetime (minutes)')));

        $this->add(new Vps_Form_Field_NumberField('width', trlVps('Width')));
        $this->add(new Vps_Form_Field_NumberField('height', trlVps('Height')));
    }
}
