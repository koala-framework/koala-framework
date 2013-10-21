<?
$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "3.5\n");
echo "Changed $file to 3.5\n";

function updateAclMenuUrls()
{
    $c = file_get_contents('app/Acl.php');
    $c = preg_replace_callback(
        '#(new Kwf_Acl_Resource_MenuUrl\\(\'([^\']+)\',(.*?)),\s*\'(/(admin|kwf|vkwf)[^\']+)\'(\s*\)\s*(,\s*\'[^\']+\'\)?);)#s',
        function($m) {
            if ('/'.str_replace('_', '/', $m[2]) == $m[4]) {
                return $m[1].$m[6];
            } else {
                return $m[0];
            }
        },
        $c
    );
    file_put_contents('app/Acl.php', $c);
    echo "updated app/Acl.php to set url only where required\n";
}

function updateHtaccess()
{
    $c = file_get_contents('.htaccess');
    $c = str_replace('RewriteRule ^(.*)$ /bootstrap.php [L]', 'RewriteRule ^(.*)$ bootstrap.php [L]', $c);
    file_put_contents('.htaccess', $c);
    echo "updated .htaccess to support running in subfolder\n";
}

function updateBootstrap()
{
    $c = file_get_contents('bootstrap.php');
    $c = str_replace("Kwf_Assets_Loader::load();\n", '', $c);

    if (file_exists('vkwf_branch')) {
        $r  = "if (file_exists('include_path')) {\n";
        $r .= "    \$path = str_replace('%vkwf_branch%', trim(file_get_contents('vkwf_branch')), trim(file_get_contents('include_path')));\n";
        $r .= "} else {\n";
        $r .= "    \$path = dirname(__FILE__).'/vkwf-lib';\n";
        $r .= "}\n";
        $c = str_replace($r, '', $c);

        $c = str_replace("require_once \$path.'/Vkwf/SetupPoi.php';\n", "require_once 'vkwf-lib/Vkwf/SetupPoi.php';\n", $c);
        $c = str_replace("require_once \$path.'/Vkwf/Setup.php';\n", "require_once 'vkwf-lib/Vkwf/Setup.php';\n", $c);


        if (!file_exists('vkwf-lib')) {
            symlink(trim(file_get_contents('include_path')), 'vkwf-lib');
            unlink('include_path');
        }
        if (!file_exists('kwf-lib')) {
            symlink(trim(file_get_contents('vkwf-lib/include_path')), 'kwf-lib');
            unlink('vkwf-lib/include_path');
        }
    }
    file_put_contents('bootstrap.php', $c);
    echo "updated bootstrap.php to remove assets loader call which is not required anymore\n";
}

updateAclMenuUrls();
updateHtaccess();
updateBootstrap();
