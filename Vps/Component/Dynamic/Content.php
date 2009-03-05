<?php
/**
 * Gibt den $content 1:1 aus.
 *
 * macht natürlich nur sinn wenn getContent mit irgendwelchen bedingungen
 * überschrieben wird
 */
class Vps_Component_Dynamic_Content
{
    protected $_content;
    public function __construct($content)
    {
        $this->_content = $content;
    }
    public function getContent()
    {
        return $this->_content;
    }
}
