<?php
/**
 * Für Komponenten die unter einer List liegen und zur ListRow Daten anzeigen.
 */
interface Vps_Data_Vpc_ListInterface extends Vps_Data_Interface
{
    public function setSubComponent($key);
    public function getSubComponent();
}
