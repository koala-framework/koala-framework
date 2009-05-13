<?php
class Vps_Update_Sql extends Vps_Update
{
    public $sql;
    public function update()
    {
        $dbConfig = Zend_Registry::get('db')->getConfig();
        $mysqlOptions = "--host={$dbConfig['host']} --user={$dbConfig['username']} --password={$dbConfig['password']} {$dbConfig['dbname']} ";
        $mysqlBinary = 'mysql';

        exec("which $mysqlBinary", $out, $ret);
        if (!$ret) {

            $descriptorspec = array(
                0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
                2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
            );
            $process = proc_open($mysqlBinary.' '.$mysqlOptions, $descriptorspec, $pipes);
            if (!is_resource($process)) {
                throw new Vps_Exception("Can't execute mysql");
            }
            fwrite($pipes[0], $this->sql);
            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $output .= stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            if (proc_close($process) != 0) {
                throw new Vps_Exception("Executing sql statement failed: ".$output);
            }
        } else {

            //fallback falls kein mysql-binary vorhanden
            //regexp von http://www.dev-explorer.com/articles/multiple-mysql-queries
            $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $this->sql); 
            foreach ($queries as $query){ 
                if (trim($query) > 0) {
                    Vps_Registry::get('db')->getConnection()->query($query);
                }
            }

        }
    }
}
