<?php
class Vps_Util_Rrd_Field
{
    private $_name;
    private $_text;
    private $_type = 'COUNTER';
    private $_min = 0;

    public function __construct($settings)
    {
        if (is_string($settings)) $settings = array('name' => $settings);
        $this->_name = $this->_escapeField($settings['name']);
        if (isset($settings['text'])) {
            $this->_text = $settings['text'];
        } else {
            $this->_text = $this->_name;
            $this->_text = str_replace('content-', '', $this->_text);
            $this->_text = str_replace('::', '-', $this->_text);
        }
        if (isset($settings['type'])) $this->_type = $settings['type'];
        if (isset($settings['max'])) {
            $this->_max = $settings['max'];
        } else {
            $this->_max = 2^31;
        }
        if (isset($settings['min'])) $this->_min = $settings['min'];
    }

    private function _escapeField($f)
    {
        if (in_array($f, array('getHits', 'getMisses', 'bytesRead', 'bytesWritten'))) return $f;

        $ret = strtolower(preg_replace('#[^a-zA-Z]#', '', $f));
        if (strlen($ret) > 19) {
            $ret = substr($ret, 0, 10).substr($ret, -9);
        }
        return $ret;
    }

    public function nameEquals($name)
    {
        $name = $this->_escapeField($name);
        return $name == $this->_name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getMin()
    {
        return $this->_min;
    }

    public function getMax()
    {
        return $this->_max;
    }
}
