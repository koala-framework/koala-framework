<?php
/**
 * Json.php
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

 * @category   Naneau
 * @package    Naneau_Action_Helper
 * @copyright  Copyright (c) 2007 Maurice Fonk - http://naneau.nl
 * @version    0.1
 */

/**
 * action helper base class
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Zend JSON
 */
require_once 'Zend/Json.php';

/**
 * Naneau_Controller_Action_Helper_Json
 * 
 * send a json result
 *
 * @category   Naneau
 * @package    Naneau_Action_Helper
 * @copyright  Copyright (c) 2007 Maurice Fonk - http://naneau.nl
 */
class Vps_Controller_Action_Helper_Json extends Zend_Controller_Action_Helper_Abstract
{
    protected $_data = array();
    /**
     * alias for addResponse()
     */
    public function direct() {
        $args = func_get_args();
        //arguments to this method
        call_user_func_array(array($this, 'addResponse'), $args);
        //call addResponse
    }

    /**
     * encode a response string and send it as JSON
     *
     * @todo remove in favor of action helper
     * @param mixed $response the string to encode
     * @param bool $useHeader put the result in an X-JSON header
     * @return void
     */
    protected function addResponse($key, $val)
    {
        if (!substr($this->getRequest()->getActionName(), 0, 4) == 'ajax') {
            throw new Vps_Controller_Exception("Can't add JSON-Response: {$this->getRequest()->getActionName()} is not an Ajax-Action");
        }
        $this->_data[$key] = $val;
    }
    
    public function preDispatch()
    {
        //disable viewRenderer if it exists
        if (substr($this->getRequest()->getActionName(), 0, 4) == 'ajax') {
            if (Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
                $viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer');
                $viewRenderer->setNoRender();
            }
        }
    }

    public function postDispatch()
    {
        if (substr($this->getRequest()->getActionName(), 0, 4) == 'ajax') {
            if(!isset($this->_data['success'])) $this->_data['success'] = true;
            $json = Zend_Json::encode($this->_data);
            $this->getResponse()->setHeader('Content-Type', 'text/javascript');
            $this->getResponse()->setBody($json);
        }
    }
}
