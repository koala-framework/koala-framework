<?php
class Vpc_Formular_Captcha_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Formular/Captcha.html');
    }
}