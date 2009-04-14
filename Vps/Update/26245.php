<?php
class Vps_Update_26245 extends Vps_Update
{
    public function update()
    {
        if (file_exists('benchmark.rrd')) {
            $fileName = 'benchmark-'.time().'.rrd';
            //$fileName = 'benchmark-old.rrd';
            rename('benchmark.rrd', $fileName);
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
            unset($out[0]);
            unset($out[1]);

            Vps_Controller_Action_Cli_BenchmarkController::createBenchmark($start);

            $newFields = Vps_Controller_Action_Cli_BenchmarkController::getFields();
            $newFields = array_merge(array('load', 'bytesRead', 'bytesWritten', 'getHits', 'getMisses'), $newFields);
            $newDatabase = '';
            foreach ($out as $line) {
                preg_match('#^([0-9]+): (.*)$#', $line, $m);
                $time = $m[1];
                $data = array();
                foreach (explode(" ", $m[2]) as $i=>$v) {
                    $data[$fields[$i]] = $v;
                }
                $newData = array();
                $newDatabase .= "                        ";
                $newDatabase .= "<!-- ".date('Y-m-d H:i:s e / U', $time)." --> <row>";
                foreach ($newFields as $f) {
                    $f = Vps_Controller_Action_Cli_BenchmarkController::escapeField($f);
                    if (isset($data[$f]) && strtolower($data[$f]) != 'nan') {
                        $i = $data[$f];
                    } else {
                        $i = 'NaN';
                    }
                    $newDatabase .= "<v> $i </v>";
                }
                $newDatabase .= " </row>\n";
            }

            $cmd = "LC_ALL=C rrdtool dump benchmark.rrd";
            $out = '';
            exec($cmd, $out, $ret);
            if ($ret) throw new Vps_Exception(implode("\n", $out));
            $out = implode("\n", $out);
            $out = preg_replace('#<database>.*</database>#s',
                    "<database>\n$newDatabase                </database>",
                    $out);
            $tmpFile = tempnam('/tmp', 'rrdupdate');
            file_put_contents($tmpFile, $out);

            $cmd = "LC_ALL=C rrdtool restore --force-overwrite $tmpFile benchmark.rrd 2>&1";
            exec($cmd, $out, $ret);
            if ($ret) throw new Vps_Exception(implode("\n", $out));

            unlink($tmpFile);
        }
    }
}
