<?php
class Vps_Util_Rrd_Field
{
    private $_name;
    private $_text;
    private $_type = 'COUNTER';
    private $_min = 0;
    private $_max;
    private $_heartbeat = null;

    /**
     * @var Vps_Util_Rrd_File
     */
    private $_file;

    public function __construct($settings)
    {
        if (is_string($settings)) $settings = array('name' => $settings);
        $this->_name = $settings['name'];
        if (isset($settings['escapedName'])) {
            if ($settings['escapedName'] != $this->_escapeField($settings['escapedName'])) {
                throw new Vps_Exception('invalid escapedName');
            }
            $this->_escapedName = $settings['escapedName'];
        } else {
            $this->_escapedName = $this->_escapeField($this->_name);
        }
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
            $this->_max = pow(2, 31);
        }
        if (isset($settings['min'])) $this->_min = $settings['min'];
        if (isset($settings['heartbeat'])) $this->_heartbeat = $settings['heartbeat'];
    }

    public static function escapeField($f)
    {
        if (in_array($f, array('getHits', 'getMisses', 'bytesRead', 'bytesWritten'))) return $f;

        $ret = strtolower(preg_replace('#[^a-zA-Z0-9]#', '', $f));
        if (strlen($ret) > 19) {
            $ret = substr($ret, 0, 10).substr($ret, -9);
        }
        return $ret;
    }

    private function _escapeField($f)
    {
        return self::escapeField($f);
    }

    public function nameEquals($name)
    {
        if ($name == $this->_name) return true;
        $name = $this->_escapeField($name);
        if ($name == $this->_escapedName) return true;
        return false;
    }

    public function getEscapedName()
    {
        return $this->_escapedName;
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

    public function getHeartbeat()
    {
        return $this->_heartbeat;
    }

    /**
     * @internal wird autom gesetzt
     */
    public function setFile(Vps_Util_Rrd_File $file)
    {
        $this->_file = $file;
    }

    public function getFile()
    {
        return $this->_file;
    }

    public final function getFileNameWithField()
    {
        return $this->getFile()->getFileName().':'.$this->getEscapedName();
    }
}
