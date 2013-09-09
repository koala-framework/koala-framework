<?
$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "master\n");
echo "Changed $file to master\n";

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        if (dirname($dir) == './kwf-lib') continue;
        if (dirname($dir) == './library') continue;
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

function replaceFiles($files, $from, $to) {
    foreach ($files as $f) {
        $content = file_get_contents($f);
        if (strpos($content, $from)) {
            file_put_contents($f, str_replace($from, $to, $content));
            echo "Change $f: $from -> $to\n";
        }
    }
}

function createTrlCacheFolder()
{
    if (!is_dir('cache/trl')) {
        mkdir('cache/trl');
        echo "folder \"cache/trl\" created\n";
    }
}


createTrlCacheFolder();

$files = glob_recursive('Events.php');
replaceFiles($files, 'Kwf_Component_Event_ComponentClass_PartialsChanged', 'Kwf_Component_Event_ComponentClass_AllPartialChanged');

