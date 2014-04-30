<?php
$file = is_file('vkwf_branch') ? 'vkwf_branch' : 'kwf_branch';
file_put_contents($file, "master\n");
echo "Changed $file to master\n";

if (!file_exists('scss')) {
    mkdir('scss');
    mkdir('scss/config');
    file_put_contents('scss/config/.gitkeep', '');
    system('git add scss/config/.gitkeep');
}

$c = file_get_contents(".gitignore");
$c = trim($c)."\nbuild\n";
file_put_contents('.gitignore', $c);
