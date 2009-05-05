<?php
class Vps_Util_Rrd_Graph
{
    private static $_defaultColors = array('#00FF00', '#999999', '#FF0000', '#00FFFF', '#0000FF', '#000000');
    private $_rrd;
    private $_verticalLabel = null;
    private $_title = null;
    private $_devideBy = null;
    private $_fields = array();

    public function __construct(Vps_Util_Rrd_File $rrd)
    {
        $this->_rrd = $rrd;
    }

    public function setVerticalLabel($l)
    {
        $this->_verticalLabel = $l;
    }

    public function setTitle($l)
    {
        $this->_title = $l;
    }

    public function setDevideBy($f)
    {
        if (is_string($f)) {
            $f = $this->_rrd->getField($f);
        }
        $this->_devideBy = $f;
    }

    public function addField($field, $color = null, $text = null)
    {
        if (is_array($field)) {
            $f = $field;
            $field = false;
            if (isset($f['field'])) $field = $f['field'];
            if ($color || $text) throw new Vps_Exception("so ned");
        } else {
            if (is_string($field)) {
                $field = $this->_rrd->getField($field);
            }
            if (is_array($color)) {
                $f = $color;
                $f['field'] = $field;
                if ($text) throw new Vps_Exception("so ned");
            } else {
                $f = array(
                    'field' => $field,
                    'color' => $color,
                    'text' => $text
                );
            }
        }
        if (!isset($f['color']) || !$f['color']) {
            foreach (self::$_defaultColors as $c) {
                $free = true;
                foreach ($this->_fields as $i) {
                    if ($i['color'] == $c) $free = false;
                }
                if ($free) {
                    $f['color'] = $c;
                    break;
                }
            }
        }
        if (!$f['color']) {
            throw new Vps_Exception("no more avaliable default colors");
        }
        if ((!isset($f['text']) || !$f['text']) && $field) {
            $f['text'] = $field->getText();
        }
        $this->_fields[] = $f;
    }

    public function getContents($start, $end = null)
    {
        if (!$end) $end = time();

        if (is_string($start)) {
            $start = strtotime($start);
        }

        $tmpFile = tempnam('/tmp', 'graph');
        $cmd = "rrdtool graph $tmpFile -h 300 -w 600 ";
        $cmd .= "-s $start ";
        $cmd .= "-e $end ";
        if ($this->_verticalLabel) {
            $cmd .= "--vertical-label \"$this->_verticalLabel\" ";
        }
        if ($this->_title) {
            $cmd .= "--title \"$this->_title\" ";
        }

        $rrdFile = $this->_rrd->getFileName();
        if ($this->_devideBy) {
            $cmd .= "DEF:requests=$rrdFile:".$this->_devideBy->getName().":AVERAGE ";
        }
        $i = 0;
        foreach ($this->_fields as $settings) {
            if (isset($settings['field'])) {
                $field = $settings['field']->getName();
                $cmd .= "DEF:line{$i}=$rrdFile:$field:AVERAGE ";
                if ($this->_devideBy) {
                    $cmd .= "CDEF:perrequest$i=line$i,requests,/ ";
                }
            }
            if (isset($settings['cmd'])) $cmd .= $settings['cmd'].' ';
            if (isset($settings['line'])) {
                $cmd .= $settings['line'].' ';
            } else {
                if ($this->_devideBy) {
                    $fieldName = "perrequest$i";
                } else {
                    $fieldName = "line$i";
                }
                $cmd .= "LINE2:$fieldName{$settings['color']}:\"$settings[text]\" ";
            }
            $i++;
        }
        $cmd .= " 2>&1";
//         d($cmd);
        exec($cmd, $out, $ret);
        if ($ret) {
            throw new Vps_Exception(implode('', $out)."\n".$cmd);
        }
        $ret = file_get_contents($tmpFile);
        unlink($tmpFile);
        return $ret;
    }

    public function output($start, $end = null)
    {
        $c = $this->getContents($start, $end);
        header('Content-Type: image/png');
        echo $c;
        exit;
    }

}
