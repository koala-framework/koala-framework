<?php
/**
 * Dieses Interface wird von Downloads Komponenten die in einem geschützten Bereich liegen
 * verwendet um zu überprüfen ob der Benutzer eingeloggt ist.
 */
interface Vps_Component_Plugin_Interface_Login
{
    public function isLoggedIn();
}
