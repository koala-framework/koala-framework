<?php
class Vps_Update_26245 extends Vps_Update
{
    public function update()
    {
        if (file_exists('benchmark.rrd')) {
            $fileName = 'benchmark-'.time().'.rrd';
            rename('benchmark.rrd', $fileName);

            //nur zum felder holen, könnte auch effizienter gemacht werden
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

            $cmd = "LC_ALL=C rrdtool dump $fileName";
            $out = array();
            exec($cmd, $out, $ret);
            if ($ret) throw new Vps_Exception(implode("\n", $out));
            $out = implode("\n", $out);

            echo "\n";
            preg_match_all('#<row>#', $out, $m);
            $progress = new Zend_ProgressBar(new Zend_ProgressBar_Adapter_Console(), 0, count($m[0]));


            $databases = array();
            while (strpos($out, '<database>') !== false) {
                $databases[] = substr($out, strpos($out, '<database>')+10, strpos($out, '</database>')-strpos($out, '<database>')-10);
                $out = substr($out, strpos($out, '</database>')+11);
            }


            $newFields = Vps_Controller_Action_Cli_BenchmarkController::getFields();
            $newFields = array_merge(array('load', 'bytesRead', 'bytesWritten', 'getHits', 'getMisses'), $newFields);
            foreach ($newFields as &$f) {
                $f = Vps_Controller_Action_Cli_BenchmarkController::escapeField($f);
            }

            foreach ($databases as &$database) {

                $pos = 0;
                $i = 0;
                while (($startPos = strpos($database, '<row>', $pos)) !== false) {
                    $progress->next();
                    $endPos = strpos($database, '</row>', $startPos);
                    $data = array();
                    $row = substr($database, $startPos+5+3, $endPos-$startPos-5-3-4);
                    foreach (explode('</v><v>', $row) as $i=>$v) {
                        $data[$fields[$i]] = $v;
                    }

                    $newData = array();
                    foreach ($newFields as $f) {
                        if (isset($data[$f]) && strtolower($data[$f]) != 'nan') {
                            $i = $data[$f];
                        } else {
                            $i = 'NaN';
                        }
                        $newData[] = $i;
                    }

                    $newRow = '<v>'.implode('</v><v>', $newData).'</v>';
                    $database = substr($database, 0, $startPos+5)
                        . $newRow
                        . substr($database, $endPos);
                    $pos = $startPos+strlen($newRow)+5;
                    $i++;
                }
            }
            $progress->finish();

            //create new rrd
            Vps_Controller_Action_Cli_BenchmarkController::createBenchmark(time());

            $cmd = "LC_ALL=C rrdtool dump benchmark.rrd";
            $out = array();
            exec($cmd, $out, $ret);
            if ($ret) throw new Vps_Exception(implode("\n", $out));
            $out = implode("\n", $out);

            $pos = 0;
            $i = 0;
            while (strpos($out, '<database>', $pos) !== false) {
                $out = substr($out, 0, strpos($out, '<database>', $pos)+10)
                    . $databases[$i]
                    . substr($out, strpos($out, '</database>', $pos));
                $pos = strpos($out, '</database>', $pos)+11;
                $i++;
            }

            $tmpFile = tempnam('/tmp', 'rrdupdate');
            $tmpFile = 'benchmark.xml';
            file_put_contents($tmpFile, $out);

            $cmd = "LC_ALL=C rrdtool restore --force-overwrite $tmpFile benchmark.rrd 2>&1";
            exec($cmd, $out, $ret);
            if ($ret) throw new Vps_Exception(implode("\n", $out));

//             unlink($tmpFile);

        }
    }
}
