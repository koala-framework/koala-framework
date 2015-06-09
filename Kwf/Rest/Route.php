<?php
class Kwf_Rest_Route extends Zend_Rest_Route
{
    public function match($request, $partial = false)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            $request = $this->_front->getRequest();
        }
        $this->_request = $request;
        $this->_setRequestKeys();

        $path   = $request->getPathInfo();
        $params = $request->getParams();
        $values = array();
        $path   = trim($path, self::URI_DELIMITER);

        if ($path != '') {

            $path = explode(self::URI_DELIMITER, $path);

            // Determine Module
            $moduleName = $this->_defaults[$this->_moduleKey];
            $dispatcher = $this->_front->getDispatcher();
            if ($dispatcher && $dispatcher->isValidModule($path[0])) {
                if ($this->_checkRestfulModule($path[0])) {
                    $values[$this->_moduleKey] = array_shift($path);
                    $this->_moduleValid = true;
                }
            }

            // Determine Controller
            $controllerName = $this->_defaults[$this->_controllerKey];
            if (count($path) && !empty($path[0])) {
                if ($this->_checkRestfulController($moduleName, $path[0])) {
                    $controllerName = $path[0];
                    $values[$this->_controllerKey] = array_shift($path);
                    $values[$this->_actionKey] = 'get';
                } else {
                    // If Controller in URI is not found to be a RESTful
                    // Controller, return false to fall back to other routes
                    return false;
                }
            } elseif ($this->_checkRestfulController($moduleName, $controllerName)) {
                $values[$this->_controllerKey] = $controllerName;
                $values[$this->_actionKey] = 'get';
            } else {
                return false;
            }
            //Store path count for method mapping
            $pathElementCount = count($path);

            // Check for "special get" URI's
            $specialGetTarget = false;
            if ($pathElementCount && array_search($path[0], array('index', 'new')) > -1) {
                $specialGetTarget = array_shift($path);
            } elseif ($pathElementCount && $path[$pathElementCount-1] == 'edit') {
                $specialGetTarget = 'edit';
                $params['id'] = $path[$pathElementCount-2];
            } elseif ($pathElementCount == 1) {
                $params['id'] = urldecode(array_shift($path));
            } elseif ($pathElementCount == 0 && !isset($params['id'])) {
                $specialGetTarget = 'index';
            }

            // Digest URI params
            if ($numSegs = count($path)) {
                for ($i = 0; $i < $numSegs; $i = $i + 2) {
                    $key = urldecode($path[$i]);
                    $val = isset($path[$i + 1]) ? urldecode($path[$i + 1]) : null;
                    $params[$key] = $val;
                }
            }

            // Determine Action
            $requestMethod = strtolower($request->getMethod());
            if ($requestMethod != 'get') {

                if ($request->getParam('_method')) {
                    $values[$this->_actionKey] = strtolower($request->getParam('_method'));
                } elseif ( $request->getHeader('X-HTTP-Method-Override') ) {
                    $values[$this->_actionKey] = strtolower($request->getHeader('X-HTTP-Method-Override'));
                } else {
                    $values[$this->_actionKey] = $requestMethod;
                }

                // Map PUT and POST to actual create/update actions
                // based on parameter count (posting to resource or collection)
                switch( $values[$this->_actionKey] ){
                    case 'post':
                        if ($pathElementCount > 0) {
                            //$values[$this->_actionKey] = 'put';
                            $values[$this->_actionKey] = 'post';
                        } else {
                            $values[$this->_actionKey] = 'post';
                        }
                        break;
                    case 'put':
                        $values[$this->_actionKey] = 'put';
                        break;
                }

            } elseif ($specialGetTarget) {
                $values[$this->_actionKey] = $specialGetTarget;
            }

        }
        $this->_values = $values + $params;

        $result = $this->_values + $this->_defaults;
        if ($partial && $result)
            $this->setMatchedPath($request->getPathInfo());

        return $result;
    }
}
