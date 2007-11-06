<?php
class Vpc_Basic_Link_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Template.html', 'Basic/Link.html');
    }
}