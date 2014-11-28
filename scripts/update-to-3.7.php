<?php
$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "3.7\n");
echo "Changed $file to 3.7\n";

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        if (dirname($dir) == './kwf-lib' || $dir == './kwf-lib') continue;
        if (dirname($dir) == './vkwf-lib' || $dir == './vkwf-lib') continue;
        if (dirname($dir) == './library' || $dir == './library') continue;
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

function updateConfig() {
    $c = file_get_contents('config.ini');
    $c = preg_replace('#^preview\.responsive#m', 'kwc.responsive', $c);
    $c = str_replace('kwc.fbAppData', 'fbAppData', $c);
    file_put_contents('config.ini', $c);
}
function createMediaMetaCacheFolder()
{
    if (!is_dir('cache/mediameta')) {
        mkdir('cache/mediameta');
        file_put_contents('cache/mediameta/.gitignore', "*\n!.gitignore\n");
        system("git add cache/mediameta/.gitignore");
        echo "folder \"cache/mediameta\" created\n";
    }
}

$files = glob_recursive('Component.php');
$files = array_merge($files, glob_recursive('config.ini'));
replaceFiles($files, 'Kwc_Columns_Component', 'Kwc_Legacy_Columns_Component');
replaceFiles($files, 'Kwc_ColumnsResponsive_Component', 'Kwc_Columns_Component');
replaceFiles($files, 'Kwc_Advanced_DyamicContent_Component', 'Kwc_Advanced_DynamicContent_Component');
updateConfig();
createMediaMetaCacheFolder();

