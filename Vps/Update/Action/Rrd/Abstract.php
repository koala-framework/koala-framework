<?php
abstract class Vps_Update_Action_Rrd_Abstract extends Vps_Update_Action_Abstract
{
    public $file;
    private $_tempFiles = array();

    protected function _systemCheckRet($cmd)
    {
        $ret = null;
        system($cmd, $ret);
        if ($ret != 0) throw new Vps_ClientException("Command failed");
    }

    public function checkSettings()
    {
        parent::checkSettings();
        if (!isset($this->file)) {
            throw new Vps_ClientException("Required parameter: file");
        }
    }

    protected function _dump()
    {
        $file = tempnam('/tmp', 'rrdupate');
        $this->_tempFiles[] = $file;
        if (!file_exists($this->file)) {
            throw new Vps_ClientException("file not found: {$this->file}");
        }
        $this->_systemCheckRet("rrdtool dump {$this->file} > $file");
        $c = file_get_contents($file);
        $c = str_replace('<!-- Round Robin Archives -->', '', $c);
        return simplexml_load_string($c);
    }

    protected function _restore($xml)
    {
        $file = tempnam('/tmp', 'rrdupate');
        $this->_tempFiles[] = $file;
        $c = $xml->asXml();
        $i = "\t";
        $c = str_replace("><name>", ">\n{$i}{$i}<name>", $c);
        $c = str_replace("><type>", ">\n{$i}{$i}<type>", $c);
        $c = str_replace("><minimal_heartbeat>", ">\n{$i}{$i}<minimal_heartbeat>", $c);
        $c = str_replace("><min>", ">\n{$i}{$i}<min>", $c);
        $c = str_replace("><max>", ">\n{$i}{$i}<max>", $c);
        $c = str_replace("><last_ds>", ">\n{$i}{$i}<last_ds>", $c);
        $c = str_replace("><value>", ">\n{$i}{$i}<value>", $c);
        $c = str_replace("><unknown_sec>", ">\n{$i}{$i}<unknown_sec>", $c);
        $c = str_replace("></ds>", ">\n{$i}</ds>", $c);
        $c = str_replace("><rra>", ">\n{$i}<rra>", $c);

        $c = str_replace("><primary_value>", ">\n{$i}{$i}{$i}<primary_value>", $c);
        $c = str_replace("><secondary_value>", ">\n{$i}{$i}{$i}<secondary_value>", $c);
        $c = str_replace("><value>", ">\n{$i}{$i}{$i}<value>", $c);
        $c = str_replace("><unknown_datapoints>", ">\n{$i}{$i}{$i}<unknown_datapoints>", $c);
        $c = str_replace("></cdp_prep>", ">\n{$i}{$i}</cdp_prep>", $c);

        file_put_contents($file, $c);
        $this->_systemCheckRet("rrdtool restore $file {$this->file}.new");
        copy($this->file, $this->file.date('Y-m-DH:i:s'));
        rename($this->file.'.new', $this->file);
    }

    public function postUpdate()
    {
        foreach ($this->_tempFiles as $f) {
            unlink($f);
        }
        return array();
    }
}
