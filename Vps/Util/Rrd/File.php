<?php
abstract class Vps_Util_Rrd_File
{
    private $_fields = array();
    private $_fileName;
    private $_timeZone = null;

    protected $_interval = 60;

    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
    }

    public function getTitle()
    {
        return $this->_fileName;
    }

    public function fileExists()
    {
        return $this->_fileName;
    }

    public function getFileName()
    {
        if (!file_exists($this->_fileName)) {
            $this->createFile();
        }
        return $this->_fileName;
    }

    public function addField($f)
    {
        if (!($f instanceof Vps_Util_Rrd_Field)) {
            $f = new Vps_Util_Rrd_Field($f);
        }
        $f->setFile($this);
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

    /**
     * erstellt rrd Datei
     *
     * wenn kein start-timestamp übergeben wird dieser automatisch ermittelt
     * und per _getInitialValueDates die datei für die vergangenheit befüllt
     *
     * @param int start-timestamp, sollte normalerweise null sein
     */
    public function createFile($start = null)
    {
        if (file_exists($this->_fileName)) {
            throw new Vps_Exception("$this->_fileName already exists");
        }

        $initialValueDates = array();
        if (is_null($start)) {
            $initialValueDates = $this->_getInitialValueDates();
            if (!$initialValueDates) {
                $start = time()-1;
            } else {
                sort($initialValueDates);
                $start = $initialValueDates[0]-1;
            }
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
            $cmd .= "DS:".$field->getEscapedName().":".$field->getType().":".$heartbeat.":".$field->getMin().":".($field->getMax())." ";
        }
        foreach ($this->getRRAs() as $rra) {
            if (!isset($rra['method'])) $rra['method'] = 'AVERAGE';
            if (!isset($rra['xff'])) $rra['xff'] = '0.6';
            $cmd .= "RRA:$rra[method]:$rra[xff]:$rra[steps]:$rra[rows] ";
        }
        //echo "$cmd<br>\n";
        system($cmd, $ret);
        if ($ret != 0) throw new Vps_Exception("Command failed");

        foreach ($initialValueDates as $date) {
            $this->record($date, $this->getRecordValuesForDate($date));
        }
    }

    protected function _getMemcacheValue($field)
    {
        $value = Vps_Benchmark_Counter::getInstance()->getValue($field);
        if ($value===false) $value = 'U';
        return $value;
    }

    /**
     * Wenn Werte für die Vergangenheit ermittelt werden können, müssen hier die
     * verfügbaren Datums zurückgeben werden
     *
     * @return array(int) timestamps
     */
    protected function _getInitialValueDates()
    {
        return array();
    }

    public function getRecordValues()
    {
        return $this->getRecordValuesForDate(time());
    }

    public function getRecordValuesForDate($date)
    {
        throw new Vps_Exception_NotYetImplemented();
    }

    /**
     * @param int wenn angegeben wird dieses datum verwendet, ansonsten NOW
     * @param array wenn angegeben werden diese values verwendet, ansonsten wird getRecordValues aufgerufen
     */
    public function record($date = null, $values = null)
    {
        if (is_null($values)) {
            if ($date) {
                $values = $this->getRecordValuesForDate($date);
            } else {
                $values = $this->getRecordValues();
            }
        }

        if (!file_exists($this->_fileName)) {
            $this->createFile();
        }

        $cmd = "rrdtool update $this->_fileName ";
        if ($date) {
            $cmd .= $date.":";
        } else {
            $cmd .= "N:";
        }
        $cmd .= implode(':', $values);

        $ret = null;
        //echo "$cmd<br>\n";
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
