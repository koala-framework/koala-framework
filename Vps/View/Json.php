<?php
class Vps_View_Json extends Vps_View
{
    private $_outputFormat = 'vpsConnection';

    public function setPlainOutputFormat()
    {
        $this->_outputFormat = '';
    }

    public function ext($class, $config = array()) {
        if ($class instanceof Vpc_Abstract) {
            if (!is_array($config)) { $config = array(); }
            $config = array_merge($config, $this->getConfig($class, array(), false));
            $class = $this->getClass($class);
        }
        $this->class = $class;
        $this->config = $config;
    }

    public function render($name)
    {
        return $this->_run();
    }

    public function getOutput()
    {
        $this->strictVars(true);
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' != substr($key, 0, 1)) {
                $out[$key] = $value;
            }
        }

        if ($this->_outputFormat == 'vpsConnection' && !isset($out['success'])) {
            $out['success'] = !isset($out['exception']) && !isset($out['error']);
        }
        return $out;
    }

    protected function _run()
    {
        return Zend_Json::encode($this->getOutput());
    }

}
