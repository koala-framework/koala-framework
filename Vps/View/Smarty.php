<?php
require_once 'Zend/View/Interface.php';
require_once 'Smarty/Smarty.class.php';

class Vps_View_Smarty implements Zend_View_Interface
{
    /**
     * Smarty object
     * @var Smarty
     */
    protected $_smarty;

    /**
     * Constructor
     *
     * @param string $tmplPath
     * @param array $extraParams
     * @return void
     */
    public function __construct($tmplPath = '../application/views', $extraParams = array())
    {
        $this->_smarty = new Smarty;

        if (null !== $tmplPath) {
            $this->setScriptPath($tmplPath);
        }
        
        if (!isset($extraParams['compile_dir'])) { $extraParams['compile_dir'] = '../application/views_c'; }
        //if (!isset($extraParams['debugging'])) { $extraParams['debugging'] = 'true'; } // TODO: Ein-/ausschaltbar machen

        foreach ($extraParams as $key => $value) {
            $this->_smarty->$key = $value;
        }

        $this->_smarty->plugins_dir[] = 'SmartyPlugins/';
    }

    /**
     * Gebe das aktuelle Template Engine Objekt zur�ck
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }
    
    /**
     * Setze den Pfad zu den Templates
     *
     * @param string $path Das Verzeichnis, das als Pfad gesetzt werden soll.
     * @return void
     */
    public function setScriptPath($path)
    {
        if (is_readable($path)) {
            $this->_smarty->template_dir = $path;
            return;
        }

        throw new Exception('Invalid path provided: ' . $path);
    }

    /**
     * Weise dem Template eine Variable zu
     *
     * @param string $key der Variablenname.
     * @param mixed $val der Variablenwert.
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_smarty->assign($key, $val);
    }

    /**
     * Hole eine zugewiesene Variable
     *
     * @param string $key der Variablenname.
     * @return mixed der Variablenwert.
     */
    public function __get($key)
    {
        return $this->_smarty->get_template_vars($key);
    }

    /**
     * Erlaubt das Testen von empty() und isset()
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return (null !== $this->_smarty->get_template_vars($key));
    }

    /**
     * Erlaubt das Zur�cksetzen von Objekteigenschaften
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->_smarty->clear_assign($key);
    }

    /**
     * Weise dem Template Variablen zu
     *
     * Erlaubt das Zuweisen eines bestimmten Wertes zu einem bestimmten Schl�ssel, ODER die 
     * �bergabe eines Array mit Schl�ssel => Wert Paaren zum Setzen in einem Rutsch.
     *
     * @see __set()
     * @param string|array $spec Die zu verwendene Zuweisungsstrategie (Schl�ssel oder Array mit 
     * Schl�ssel => Wert paaren)
     * @param mixed $value (Optional) Wenn ein Variablenname verwendet wurde, verwende dies als den
     * Wert.
     * @return void
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            $this->_smarty->assign($spec);
            return;
        }

        $this->_smarty->assign($spec, $value);
    }

    /**
     * Setze alle zugewiesenen Variablen zur�ck.
     *
     * Setzt alle Variablen zur�ck, die Zend_View entweder durch {@link assign()} oder
     * �berladen von Eigenschaften ({@link __get()}/{@link __set()}) zugewiesen worden sind.
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_smarty->clear_all_assign();
    }

    /**
     * Verarbeitet ein Template und gibt die Ausgabe zur�ck
     *
     * @param string $name Das zu verarbeitende Template
     * @return string Die Ausgabe.
     */
    public function render($name)
    {
        return $this->_smarty->fetch($name);
    }
}
