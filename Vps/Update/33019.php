<?php
class Vps_Update_33019 extends Vps_Update
{
    public function update()
    {
        $c = file_get_contents('bootstrap.php');
        if (strpos($c, "define('VPS_PATH', dirname(__FILE__).'/vps-lib')")===false && strpos($c, "503 Service Unavailable")===false) {
            $c = explode("\n", $c);
            foreach ($c as $k=>$i) {
                $i = trim($i);
                if ($i == '<?php' || $i == 'chdir(dirname(__FILE__));') {
                    unset($c[$k]);
                }
                if ($i == "if (file_exists('application/include_path')) {") {
                    unset($c[$k]);
                    unset($c[$k+1]);
                    unset($c[$k+2]);
                    unset($c[$k+3]);
                    unset($c[$k+4]);
                }
            }
$newContent = "<?php
chdir(dirname(__FILE__));
if (file_exists('application/include_path')) {
    define('VPS_PATH', str_replace('%vps_branch%', trim(file_get_contents('application/vps_branch')), trim(file_get_contents('application/include_path'))));
} else {
    define('VPS_PATH', dirname(__FILE__).'/vps-lib');
}
";
            $newContent = $newContent . implode("\n", $c);
            file_put_contents('bootstrap.php', $newContent);

            echo "\n\n\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";
            echo "ACHTUNG bootstrap.php wurde angepasst, bitte UMBEDINGT ueberpruefen ob das eh alles stimmt.\n";
        }

    }
}
