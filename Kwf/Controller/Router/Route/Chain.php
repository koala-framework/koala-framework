<?php
class Kwf_Controller_Router_Route_Chain extends Zend_Controller_Router_Route_Chain
{
    //zend implementation overridden to avoid pathInfo getting modified even if route didn't match
    public function match($request, $partial = null)
    {
        $path        = trim($request->getPathInfo(), self::URI_DELIMITER);
        $subPath     = $path;
        $values      = array();
        $numRoutes   = count($this->_routes);
        $matchedPath = null;

        foreach ($this->_routes as $key => $route) {
            if ($key > 0
                && $matchedPath !== null
                && $subPath !== ''
                && $subPath !== false
            ) {
                $separator = substr($subPath, 0, strlen($this->_separators[$key]));

                if ($separator !== $this->_separators[$key]) {
                    return false;
                }

                $subPath = substr($subPath, strlen($separator));
            }

            // TODO: Should be an interface method. Hack for 1.0 BC
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $subPath;
            } else {
                $request->setPathInfo($subPath);
                $match = $request;
            }

            $res = $route->match($match, true, ($key == $numRoutes - 1));
            if ($res === false) {
//BEGIN FIX
                $request->setPathInfo($path);
//END FIX
                return false;
            }

            $matchedPath = $route->getMatchedPath();

            if ($matchedPath !== null) {
                $subPath     = substr($subPath, strlen($matchedPath));
                $separator   = substr($subPath, 0, strlen($this->_separators[$key]));
            }

            $values = $res + $values;
        }

        $request->setPathInfo($path);

        if ($subPath !== '' && $subPath !== false) {
            return false;
        }

        return $values;
    }
}
