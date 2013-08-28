<?php
class Kwf_Benchmark_Counter_File
{
    private $_lock = null;

    private function _lock($name)
    {
        if ($this->_lock) {
            throw new Kwf_Exception("Already locked");
        }
        if (!file_exists('temp/counter')) {
            mkdir('temp/counter');
        }
        $file = 'temp/counter/'.$name.'.lock';
        $this->_lock = fopen($file, 'w');
        flock($this->_lock, LOCK_EX);
    }

    private function _unlock()
    {
        fclose($this->_lock);
        $this->_lock = null;
    }

    private function _open($name, $mode)
    {
        $file = 'temp/counter/'.$name;
        if (!file_exists($file)) {
            if ($mode == 'r') {
                return false;
            }
        }
        $fp = fopen($file, $mode);
        return $fp;
    }

    public function increment($name, $value=1)
    {
        $this->_lock($name);
        $v = $this->getValue($name);
        $v = $v + $value;
        $fp = $this->_open($name, 'w');
        fwrite($fp, $v);
        fclose($fp);
        $this->_unlock();
    }

    public function getValue($name)
    {
        $fp = $this->_open($name, 'r');
        $v = false;
        if ($fp) {
            $v = fread($fp, 1024);
            fclose($fp);
        }
        return $v;
    }
}
