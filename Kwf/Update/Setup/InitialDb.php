<?php
class Kwf_Update_Setup_InitialDb extends Kwf_Update_Sql
{
    public function __construct()
    {
        parent::__construct(null, null);
    }

    public function update()
    {
        $file = 'setup/setup.sql'; //initial setup for web
        if (file_exists($file)) {
            $this->sql = file_get_contents($file);
            parent::update();
        }
    }
}
