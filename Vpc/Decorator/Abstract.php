<?php
/**
 * Basisklasse für Decorators
 * @package Vpc
 * @subpackage Decorator
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
abstract class Vpc_Decorator_Abstract implements Vpc_Interface
{
    protected $_component;

    /**
     * Ein Decorator kann im Gegensatz zu einer Komponenten direkt im
     * Konstruktor erstellt werden, da die Eigenschaften der Komponente
     * ohnehin durchgeschleift werden.
     * 
     * @param Vps_Dao DAO
     * @param Vpc_Interface Komponente, die dekoriert werden soll
     */
    public function __construct(Vpc_Interface $component)
    {
        $this->_component = $component;
    }
    
    public static function getSettings()
    {
        return array();
    }

    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function getTemplateVars()
    {
        return $this->_component->getTemplateVars();
    }

    public function getSearchVars()
    {
        return $this->_component->getSearchVars();
    }
    
    public function getStatisticVars()
    {
        return $this->_component->getStatisticVars();
    }

    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function getTreeCacheRow()
    {
        return $this->_component->getTreeCacheRow();
    }

    /**
     * @return Vps_Dao DAO
     */
    protected function getDao()
    {
        return $this->_component->getDao();
    }

    /**
     * Shortcut für $this->_dao->getTable($tablename)
     * @param string Name des Models
     */
    protected function _getTable($tablename)
    {
        return $this->_dao->getTable($tablename);
    }

    public static function getSetting($class, $setting)
    {
        if (!Vps_Loader::classExists($class)) {
            $class = substr($class, 0, strrpos($class, '_')) . '_Component';
        }
        if (class_exists($class)) {
            $settings = call_user_func(array($class, 'getSettings'));
            return isset($settings[$setting]) ? $settings[$setting] : null ;
        } else {
            return null;
        }
    }

    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    /**
     * Schleift die Methode auf auf dekorierte Komponente durch.
     */
    public function getUrl()
    {
        return $this->_component->getUrl();
    }
    public function sendContent($decoratedPage)
    {
        $this->_component->sendContent($decoratedPage);
    }
    /**
     * Shortcut, fragt vom Seitenbaum, ob die unsichtbaren Einträge
     * auch angezeige werden
     *
     * @return boolean
     */
    protected function _showInvisible()
    {
        return $this->getTreeCacheRow()->getTable()->showInvisible();
    }

}
