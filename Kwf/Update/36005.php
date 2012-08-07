<?php
class Kwf_Update_36005 extends Kwf_Update
{
    public function getTags()
    {
        return array('fulltext');
    }

    public function update()
    {
        if (is_instance_of(Kwf_Registry::get('config')->fulltext->backend, 'Kwf_Util_Fulltext_Backend_ZendSearch')) {
            if (!file_exists('cache/fulltext')) {
                mkdir('cache/fulltext');
                file_put_contents('cache/fulltext/.gitignore', "*\n");
                system("git add -f cache/fulltext/.gitignore");
            }
        }
    }
}
