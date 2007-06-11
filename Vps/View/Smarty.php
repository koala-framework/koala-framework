<?php
/**
 * 
 * Vps_View_Smarty
 * 
 * using the Smarty templating engine with the zend framework
 * 
 * Copyright (c) 2007, Maurice Fonk
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY MAURICE FONK ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <copyright holder> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category   Naneau
 * @package    Naneau_View
 * @copyright  Copyright (c) 2007 Maurice Fonk - http://naneau.nl
 * @version    0.2
 */

/**
 * Zend View Base Class
 */
require_once 'Zend/View.php';

/**
 * Smarty templating engine
 */
require_once 'Smarty/Smarty.class.php';

/**
 * @category   Naneau
 * @package    Naneau_View
 * @copyright  Copyright (c) 2007 Maurice Fonk - http://naneau.nl
 */
class Vps_View_Smarty extends Zend_View_Abstract
{
    /**
     * Smarty object
     * @var Smarty
     */
    protected $_smarty;

    /**
     * Constructor
     * 
     * Pass it a an array with the following configuration options:
     * 
     * scriptPath: the directory where your templates reside
     * compileDir: the directory where you want your compiled templates (must be
     * writable by the webserver)
     * configDir: the directory where your configuration files reside
     * 
     * both scriptPath and compileDir are mandatory options, as Smarty needs
     * them. You can't set a cacheDir, if you want caching use Zend_Cache
     * instead, adding caching to the view explicitly would alter behaviour
     * from Zend_View.
     * 
     * @see Zend_View::__construct
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = array())
    {
        $this->_smarty = new Smarty();

        $this->_smarty->compile_dir = '../application/views_c';
        $this->_smarty->plugins_dir[] = 'SmartyPlugins';
// trigger_error(print_r($config, true));
        parent::__construct($config);
    }

    /**
     * Return the template engine object
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }

    /**
     * fetch a template, echos the result,
     * 
     * @see Zend_View_Abstract::render()
     * @param string $name the template
     * @return void
     */
    protected function _run()
    {
        $this->strictVars(true);

        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' != substr($key, 0, 1)) {
                $this->_smarty->assign($key, $value);
            }
        }
        //assign variables to the template engine

        $this->_smarty->assign_by_ref('this', $this);
        //why 'this'?
        //to emulate standard zend view functionality
        //doesn't mess up smarty in any way

        $path = $this->getScriptPaths();
        
        $file = substr(func_get_arg(0), strlen($path[0]));
        //smarty needs a template_dir, and can only use templates,
        //found in that directory, so we have to strip it from the filename

        $this->_smarty->template_dir = $path[0];
        //set the template diretory as the first directory from the path

        echo $this->_smarty->fetch($file);
        //process the template (and filter the output)
    }
}
