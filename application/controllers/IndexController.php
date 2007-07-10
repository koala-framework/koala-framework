<?php
class IndexController extends Vps_Controller_Action
{
    public function indexAction()
    {
        echo '<a href="http://vpsdoc.vivid">Api-Dokumentation</a><br />';
        echo '<a href="http://vps.vivid/components">Komponentenübersicht</a>';
    }
}
