<?php
/**
 * Für Komponenten die unter einer List liegen und zur ListRow Daten anzeigen.
 */
interface Kwf_Data_Kwc_ListInterface extends Kwf_Data_Interface
{
    public function setSubComponent($key);
    public function getSubComponent();
}
