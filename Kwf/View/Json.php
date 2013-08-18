<?php
class Kwf_View_Json extends Zend_View_Abstract
{
    private $_outputFormat = 'kwfConnection';

    public function setPlainOutputFormat()
    {
        $this->_outputFormat = '';
    }

    public function kwc($config)
    {
        $this->config = $config;
    }

    public function ext($class, $config = array()) {
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

        if ($this->_outputFormat == 'kwfConnection' && !isset($out['success'])) {
            $out['success'] = !isset($out['exception']) && !isset($out['error']);
        }
        return $out;
    }

    protected function _run()
    {
        return Zend_Json::encode($this->getOutput());
    }

}
