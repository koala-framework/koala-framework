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

updateAclMenuUrls();
updateHtaccess();
