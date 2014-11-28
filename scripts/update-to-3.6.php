<?php
$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "3.6\n");
echo "Changed $file to 3.6\n";

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

function createTrlCacheFolder()
{
    if (!is_dir('cache/trl')) {
        mkdir('cache/trl');
        file_put_contents('cache/trl/.gitignore', "*\n!.gitignore\n");
        system("git add cache/trl/.gitignore");
        echo "folder \"cache/trl\" created\n";
    }
}
function createScssCacheFolder()
{
    if (!is_dir('cache/scss')) {
        mkdir('cache/scss');
        file_put_contents('cache/scss/.gitignore', "*\n!.gitignore\n");
        system("git add cache/scss/.gitignore");
        echo "folder \"cache/scss\" created\n";
    }
}

function updateErrorViews($files)
{
    foreach ($files as $f) {
        $content = file_get_contents($f);
        $origContent = $content;
        $content = preg_replace('#<\?=\s*trl#', '<'.'?=$this->data->trl', $content);
        if ($origContent != $content) {
            file_put_contents($f, $content);
            echo "Change $f: update trl\n";
        }
    }
}

function checkFileForCropParameter($path)
{
    $c = file_get_contents($path);
    $parameterChanged = false;
    $c = preg_replace_callback(
        '#\'scale\'\s*=>\s*Kwf_Media_Image::SCALE_BESTFIT#s',
        function($m) use (&$parameterChanged) {
            $parameterChanged = true;
            return '\'cover\' => false';
        },
        $c
    );
    $c = preg_replace_callback(
        '#\'scale\'\s*=>\s*Kwf_Media_Image::SCALE_ORIGINAL#s',
        function($m) use (&$parameterChanged) {
            $parameterChanged = true;
            return '\'cover\' => false';
        },
        $c
    );
    $c = preg_replace_callback(
        '#\'scale\'\s*=>\s*Kwf_Media_Image::SCALE_CROP#s',
        function($m) use (&$parameterChanged) {
            $parameterChanged = true;
            return '\'cover\' => true';
        },
        $c
    );
    $c = preg_replace_callback(
        '#\'scale\'\s*=>\s*Kwf_Media_Image::SCALE_DEFORM#s',
        function($m) use (&$parameterChanged) {
            $parameterChanged = true;
            return '\'cover\' => true';
        },
        $c
    );
    if ($parameterChanged) {
        file_put_contents($path, $c);
    }
}

function recursiveCropImageOptionsReplace($directory)
{
    if (!is_dir($directory)) {
        return false;
    }
    $files = scandir($directory);
    foreach ($files as $file) {
        $path = $directory.'/'.$file;
        if ($file != '.' && $file != '..' && !recursiveCropImageOptionsReplace($path)) {
            $info = pathinfo($path, PATHINFO_EXTENSION);
            if ($info == 'php' || $info == 'yml' ) {
                checkFileForCropParameter($path);
            }
        }
    }
}

function updateIncludePath()
{
    $defaultPaths = array(
        'app', 'controllers', 'models', 'components', 'themes'
    );
    $addConfig = "";

    $c = file_get_contents('bootstrap.php');
    preg_match_all('#\$include_path\s*\.=\s*PATH_SEPARATOR\s*\.\s*\'([^\']+)\';'."\n?#", $c, $m);
    $c = str_replace('$include_path  = get_include_path();'."\n", '', $c);
    $c = str_replace('set_include_path($include_path);'."\n", '', $c);
    foreach ($m[0] as $k=>$i) {
        $c = str_replace($i, '', $c);
        $path = $m[1][$k];
        if (!in_array($path, $defaultPaths)) {
            $addConfig = "includepath.web".ucfirst($path)." = $path\n";
        }
    }
    file_put_contents('bootstrap.php', $c);
    echo "removed include paths from bootstrap.php\n";

    if ($addConfig) {
        $c = file_get_contents('config.ini');
        $c = str_replace("[production]\n", "[production]\n".$addConfig, $c);
        file_put_contents('config.ini', $c);
        echo "added custom include paths to config.ini\n";
    }
}

createTrlCacheFolder();
createScssCacheFolder();

$files = glob_recursive('Events.php');
replaceFiles($files, 'Kwf_Component_Event_ComponentClass_PartialsChanged', 'Kwf_Component_Event_ComponentClass_AllPartialChanged');

$files = glob_recursive('Component.php');
$files = array_merge($files, glob_recursive('config.ini'));
replaceFiles($files, 'Kwc_Basic_Headlines_Component', 'Kwc_Legacy_Headlines_Component');
replaceFiles($files, 'Kwc_Basic_Headline_Component', 'Kwc_Legacy_Headline_Component');

updateErrorViews(glob('views/error*.tpl'));

echo "Update Image-Parameter\n";
recursiveCropImageOptionsReplace('components');

updateIncludePath();
