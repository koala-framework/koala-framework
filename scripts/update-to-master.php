<?php
$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "master\n");
echo "Changed $file to master\n";


function deleteCacheFolder($path)
{
    if (!file_exists($path)) return;
    system("git rm $path/.gitignore");
    clearstatcache();
    if (!file_exists($path)) return;

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $file) {
        if (in_array($file->getBasename(), array('.', '..'))) {
            continue;
        } elseif ($file->isDir()) {
            rmdir($file->getPathname());
        } elseif ($file->isFile() || $file->isLink()) {
            unlink($file->getPathname());
        }
    }
    rmdir($path);
}

if (!file_exists('scss')) {
    mkdir('scss');
    mkdir('scss/config');
    file_put_contents('scss/config/.gitkeep', '');
    system('git add scss/config/.gitkeep');
}

if (!is_dir('cache/uglifyjs')) {
    mkdir('cache/uglifyjs');
    file_put_contents('cache/uglifyjs/.gitignore', "*\n!.gitignore\n");
    system("git add cache/uglifyjs/.gitignore");
    echo "folder \"cache/uglifyjs\" created\n";
}

deleteCacheFolder('cache/events');
deleteCacheFolder('cache/table');
deleteCacheFolder('cache/trl');

$c = file_get_contents(".gitignore");
$c = trim($c)."\nbuild\n";
file_put_contents('.gitignore', $c);
