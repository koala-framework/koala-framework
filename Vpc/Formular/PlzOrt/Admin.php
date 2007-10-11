<?php
class Vpc_Formular_PlzOrt_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Formular/PlzOrt.html');
    }
}