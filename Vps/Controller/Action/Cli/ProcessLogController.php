<?php
class Vps_Controller_Action_Cli_ProcessLogController extends Vps_Controller_Action
{
    public static function getHelp()
    {
        return "process error log";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'type',
                'value'=> array('error', 'accessdenied', 'notfound'),
                'valueOptional' => true
            )
        );
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
            if ($this->_getParam('type') && $dir != $this->_getParam('type')) continue;
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
                        'uri' => $this->_getValueFromLogContents('REQUEST_URI', $c),
                        'message' => $this->_getValueFromLogContents('Message', $c)
                    );
                }
            }
        }
        $i = 0;
        arsort($times);
        foreach (array_keys($times) as $k) {
            $i++;
            echo "\nprocessing log entry $i/".count($entries).":\n";
            $entry = $entries[$k];
            echo "Time: ".date("Y-m-d H:i:s", $entry['time'])."\n";
            echo "Uri: $entry[uri]\n";
            echo "Trown: $entry[thrown]\n";
            echo "Message: $entry[message]\n";
            while (true) {
                echo "(d)elete (s)how (i)gnore (q)uit";
                $stdin = fopen('php://stdin', 'r');
                $input = fgets($stdin, 2);
                fclose($stdin);
                echo "\n";
                if ($input == 'd') {
                    unlink($entry['file']);
                    break;
                } else if ($input == 's') {
                    echo "\n\n".str_repeat('-', 80)."\n";
                    echo file_get_contents($entry['file']);
                } else if ($input == 'i') {
                    break;
                } else if ($input == 'q') {
                    exit;
                }
            }
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
