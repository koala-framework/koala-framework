<?php
class Kwf_Update_Sql extends Kwf_Update
{
    public $sql;
    public function update()
    {
        $dbConfig = Zend_Registry::get('db')->getConfig();
        $mysqlOptions = "--host=".escapeshellarg($dbConfig['host'])." --user=".escapeshellarg($dbConfig['username'])." --password=".escapeshellarg($dbConfig['password'])." ".escapeshellarg($dbConfig['dbname'])." ";
        $mysqlBinary = 'mysql';

        exec("which $mysqlBinary 2>&1", $out, $ret);
        if (!$ret) {

            $descriptorspec = array(
                0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
                1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
                2 => array("pipe", "w"),  // stderr is a pipe that the child will write to
            );
            $process = proc_open($mysqlBinary.' '.$mysqlOptions, $descriptorspec, $pipes);
            if (!is_resource($process)) {
                throw new Kwf_Exception("Can't execute mysql");
            }
            fwrite($pipes[0], $this->sql);
            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $output .= stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            if (proc_close($process) != 0) {
                throw new Kwf_Exception("Executing '$this->_uniqueName' sql statement failed: ".$output);
            }
        } else {

            //fallback falls kein mysql-binary vorhanden
            //regexp von http://www.dev-explorer.com/articles/multiple-mysql-queries
            $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $this->sql); 
            foreach ($queries as $query){ 
                if (trim($query)) {
                    Kwf_Registry::get('db')->getConnection()->query($query);
                }
            }

        }
    }
}
