<?php
abstract class Vps_Util_Rrd_File
{
    private $_fields = array();
    private $_fileName;
    private $_interval = 60;
    private $_timeZone = null;

    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
    }

    public function getTitle()
    {
        return $this->getFileName();
    }

    public function getFileName()
    {
        return $this->_fileName;
    }

    public function addField($f)
    {
        if (!($f instanceof Vps_Util_Rrd_Field)) {
            $f = new Vps_Util_Rrd_Field($f);
        }
        $this->_fields[] = $f;
    }

    public function getField($name)
    {
        foreach ($this->_fields as $f) {
            if ($f->nameEquals($name)) return $f;
        }
        return null;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function setInterval($i)
    {
        $this->_interval = $i;
    }

    //im moment nur für graph
    public function setTimeZone($i)
    {
        $this->_timeZone = $i;
    }

    public function getTimeZone()
    {
        return $this->_timeZone;
    }

    protected function getRRAs()
    {
        return array(
            array(
                //1 woche (bei interval von 60)
                'steps' => 1,
                'rows' => 2016
            ),
            array(
                //1 monat (bei interval von 60)
                'steps' => 7,
                'rows' => 1500
            ),
            array(
                //1 jahr (bei interval von 60)
                'steps' => 50,
                'rows' => 2500
            ),
        );
    }

    public function createFile($start)
    {
        if (file_exists($this->_fileName)) {
            throw new Vps_Exception("$this->_fileName already exists");
        }
        $cmd = "rrdtool create $this->_fileName ";
        $cmd .= "--start ".$start." ";
        $cmd .= "--step ".($this->_interval)." ";
        foreach ($this->_fields as $field) {
            if (!is_null($field->getHeartbeat())) {
                $heartbeat = $field->getHeartbeat();
            } else {
                $heartbeat = ($this->_interval*2);
            }
            $cmd .= "DS:".$field->getName().":".$field->getType().":".$heartbeat.":".$field->getMin().":".($field->getMax())." ";
        }
        foreach ($this->getRRAs() as $rra) {
            if (!isset($rra['method'])) $rra['method'] = 'AVERAGE';
            if (!isset($rra['xff'])) $rra['xff'] = '0.6';
            $cmd .= "RRA:$rra[method]:$rra[xff]:$rra[steps]:$rra[rows] ";
        }
        system($cmd, $ret);
        if ($ret != 0) throw new Vps_Exception("Command failed");
    }

    protected function _getMemcacheValue($field)
    {
        $value = Vps_Benchmark_Counter::getInstance()->getValue($field);
        if ($value===false) $value = 'U';
        return $value;
    }

    public function getRecordValues()
    {
        return $this->getRecordValuesForDate(time());
    }

    public function getRecordValuesForDate($date)
    {
        throw new Vps_Exception_NotYetImplemented();
    }

    public function record($date = null)
    {
        if ($date) {
            $values = $this->getRecordValuesForDate($date);
        } else {
            $values = $this->getRecordValues();
        }

        if (!file_exists($this->_fileName)) {
            $this->createFile(time()-1);
        }

        $cmd = "rrdtool update $this->_fileName ";
        if ($date) {
            $cmd .= $date.":";
        } else {
            $cmd .= "N:";
        }
        $cmd .= implode(':', $values);

        $ret = null;
        system($cmd, $ret);
        if ($ret != 0) throw new Vps_Exception("Command failed");
    }

    public function getAverageValues($fields, $start, $end)
    {
        if (!file_exists('stats.rrd')) {
            $ret = array();
            foreach ($fields as $f) {
                $ret[$f] = 0;
            }
        }
        $cmd = "rrdtool fetch stats.rrd AVERAGE --start $start --end $end 2>&1";
        exec($cmd, $rows);

        foreach ($fields as $f) {
            $sum[$f] = 0;
            $cnt[$f] = 0;
        }

        foreach ($rows as $k=>$r) {
            preg_match_all('#[^ ]+#', $r, $m);
            $r = $m[0];
            if ($k == 0) {
                $fileFields = $r;
            } else if ($k == 1) {
                //leerzeile
            } else {
                if (count($fileFields)+1 != count($r)) {
                    throw new Vps_Exception("invalid row?!");
                }
                $time = array_shift($r);
                $time = (int)substr($time, 0, -1);
                foreach ($fields as $f) {
                    $v = $r[array_search(Vps_Util_Rrd_Field::escapeField($f), $fileFields)];
                    if (preg_match('#^([0-9]\\.[0-9]+)e([+-])([0-9]{2})$#', $v, $m)) {
                        $v = (float)$m[1] * ($m[2]=='-' ? -1 : 1) * pow(10, (int)$m[3]);
                        $sum[$f] += $v;
                        $cnt[$f]++;
                    }
                }
            }
        }
        $ret = array();
        foreach ($fields as $f) {
            if ($cnt[$f]) {
                $ret[$f] = $sum[$f] / $cnt[$f];
            } else {
                $ret[$f] = 0;
            }
        }
        return $ret;
    }
}
