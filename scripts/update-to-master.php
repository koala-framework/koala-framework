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

function checkFileForCropParameter($path)
{
    $c = file_get_contents($path);
    $parameterChanged = false;
    $c = preg_replace_callback(
        '#\'scale\' => Kwf_Media_Image::SCALE_BESTFIT#s',
        function($m) {
            $parameterChanged = true;
            return '\'cover\' => false';
        },
        $c
    );
    $c = preg_replace_callback(
        '#\'scale\' => Kwf_Media_Image::SCALE_ORIGINAL#s',
        function($m) {
            $parameterChanged = true;
            return '\'cover\' => false';
        },
        $c
    );
    $c = preg_replace_callback(
        '#\'scale\' => Kwf_Media_Image::SCALE_CROP#s',
        function($m) {
            $parameterChanged = true;
            return '\'cover\' => true';
        },
        $c
    );
    $c = preg_replace_callback(
        '#\'scale\' => Kwf_Media_Image::SCALE_DEFORM#s',
        function($m) {
            $parameterChanged = true;
            return '\'cover\' => true';
        },
        $c
    );
    if ($parameterChanged) {
        file_put_contents($path, $c);
//         echo "\t\tParameter changed\n";
//     } else {
//         echo "\t\tNothing had to be changed\n";
    }
}

function recursiveCropImageOptionsReplace($directory)
{
//     echo "\trecursive head $directory\n";
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
//                 echo "\tupdated $path to match new parameter\n";
            }
        }
    }
}

createTrlCacheFolder();

$files = glob_recursive('Events.php');
replaceFiles($files, 'Kwf_Component_Event_ComponentClass_PartialsChanged', 'Kwf_Component_Event_ComponentClass_AllPartialChanged');

echo "Update Image-Parameter\n";
recursiveCropImageOptionsReplace('components');
echo "Finished changing Image-Parameters\n";
