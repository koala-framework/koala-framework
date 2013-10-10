<?
$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "master\n");
echo "Changed $file to master\n";

function createTrlCacheFolder()
{
    if (!is_dir('cache/trl')) {
        mkdir('cache/trl');
        echo "folder \"cache/trl\" created\n";
    }
}

createTrlCacheFolder();
