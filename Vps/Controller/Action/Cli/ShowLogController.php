<?php
class Vps_Controller_Action_Cli_ShowLogController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "show error log";
    }
    public function indexAction()
    {
        $logDir = 'application/log';

        $entries = array();
        $times = array();
        foreach (new DirectoryIterator($logDir) as $dir) {
            if (!$dir->isDir()) continue;
            if ($dir->isDot()) continue;
            if ($dir == '.svn') continue;
            foreach (new DirectoryIterator($dir->getPathname()) as $date) {
                if (!$date->isDir()) continue;
                if ($date->isDot()) continue;
                if (!($dateTimestamp = strtotime($date->__toString()))) continue;
                foreach (new DirectoryIterator($date->getPathname()) as $file) {
                    if ($file->isDir()) continue;
                    if (!preg_match('#^([0-9]{2})_([0-9]{2})_([0-9]{2})#', $file->__toString(), $m)) continue;
                    $timestamp = $dateTimestamp + $m[3] + $m[2]*60 + $m[1]*60*60;
                    $times[] = $timestamp;
                    $c = file_get_contents($file->getPathname());
                    $entries[] = array(
                        'file' => $file->getPathname(),
                        'type' => $dir->__toString(),
                        'time' => $timestamp,
                        'thrown' => $this->_getValueFromLogContents('Thrown', $c),
                        'uri' => $this->_getValueFromLogContents('REQUEST_URI', $c)
                    );
                }
            }
        }
        $i = 0;
        arsort($times);
        foreach (array_keys($times) as $k) {
            $i++;
            $entry = $entries[$k];
            echo substr($entry['file'], strlen($logDir)+1).' '.$entry['uri'].' '.$entry['thrown']."\n";
            //echo $i.": ".date("Y-m-d H:i:s", $entry['time']).' '.$entry['type']."\n";
        }
        exit;
    }
    private function _getValueFromLogContents($field, $c)
    {
        $field = preg_quote($field, '#');
        if (preg_match("#\\*\\* ".$field." \\*\\*(.*)-- ".$field." --#s", $c, $m)) {
            return trim($m[1]);
        }
        return '';
    }
}
