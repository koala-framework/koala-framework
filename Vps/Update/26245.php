<?php
class Vps_Update_26245 extends Vps_Update
{
    private static $_fields;
    private static $_progress;
    private static $_curProgress;
    public function update()
    {
        if (file_exists('benchmark.rrd')) {
            $fileName = 'benchmark-'.time().'.rrd';
            //$fileName = 'benchmark-old.rrd';
            rename('benchmark.rrd', $fileName);
            /*
            $start = trim(`rrdtool first $fileName`);
            $end = trim(`rrdtool last $fileName`);
            $cmd = "LC_ALL=C rrdtool fetch $fileName AVERAGE -s $start -e $end 2>&1";
            exec($cmd, $out, $ret);
            if ($ret) throw new Vps_Exception(implode("\n", $out));
            $fields = array();
            $out[0] = trim(preg_replace('#  +#', ' ', $out[0]));
            foreach (explode(' ', $out[0]) as $f) {
                $fields[] = trim($f);
            }
            self::$_fields = $fields;

            $cmd = "LC_ALL=C rrdtool dump $fileName";
            $out = '';
            exec($cmd, $out, $ret);
            if ($ret) throw new Vps_Exception(implode("\n", $out));
            $out = implode("\n", $out);

            preg_match_all('#<row>#', $out, $m);
            self::$_progress = new Zend_ProgressBar(new Zend_ProgressBar_Adapter_Console(), 0, count($m[0]));
            $out = preg_replace_callback('#<row><v>(.*?)</v></row>#',
                    "Vps_Update_26245::_dbCallback",
                    $out);
            self::$_progress->finish();

            $tmpFile = tempnam('/tmp', 'rrdupdate');
            $tmpFile = 'benchmark.xml';
            file_put_contents($tmpFile, $out);

            $cmd = "LC_ALL=C rrdtool restore --force-overwrite $tmpFile benchmark.rrd 2>&1";
            exec($cmd, $out, $ret);
            if ($ret) throw new Vps_Exception(implode("\n", $out));

//             unlink($tmpFile);
            */
        }
    }
    /*
    static public function _dbCallback($m)
    {
        self::$_curProgress++;
        if (self::$_curProgress % 20) {
            self::$_progress->update(self::$_curProgress);
        }
        $newFields = Vps_Controller_Action_Cli_BenchmarkController::getFields();
        $newFields = array_merge(array('load', 'bytesRead', 'bytesWritten', 'getHits', 'getMisses'), $newFields);

        $data = array();
        foreach (explode('</v><v>', $m[1]) as $i) {
            foreach (self::$_fields as $f) {
                $data[$f] = $i;
            }
        }

        $newData = array();
        foreach ($newFields as $f) {
            $f = Vps_Controller_Action_Cli_BenchmarkController::escapeField($f);
            if (isset($data[$f]) && strtolower($data[$f]) != 'nan') {
                $i = $data[$f];
            } else {
                $i = 'NaN';
            }
            $newData[] = $i;
        }
        return '<row><v>'.implode('</v><v>', $newData).'</v></row>';
    }
    */
}
