<?php
class Vps_Update_33020 extends Vps_Update
{
    protected $_tags = array('vps'); //nur "echte" vps webs, wird für vw usw nicht gemacht

    public function update()
    {
        if (!file_exists('.git')) {
            //echo "\n\n\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
            //echo "ACHTUNG web (und eventuell vps) wurden auf git umgestellt.\n";
            //system("php bootstrap.php git convert-to-git");
        }
    }
}
