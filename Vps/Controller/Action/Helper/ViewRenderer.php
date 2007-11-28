<?p
class Vps_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_Abstra

    var $view = nul
    var $_noRender = fals

    public function __construct(Zend_View_Interface $view = nul
   
        if (null !== $view)
            $this->setView($view
       
   

    public function setView(Zend_View_Interface $vie
   
        $this->view = $vie
        return $thi
   

    public function setNoRender($noRender = tru
   
        $this->_noRender = $noRende
   

    public function preDispatch()
        $module = $this->getRequest()->getParam('module'
        if ($this->isJson())
            $this->view = new Vps_View_Json(
        } else
            $this->view = new Vps_View_Smarty(
       

        if ((null !== $this->_actionController) && (null === $this->_actionController->view))
            $this->_actionController->view = $this->vie

            if ($module == 'component')
                $request = $this->getRequest(
                $pageId = $request->getParam('page_id'
                $componentKey = $request->getParam('component_key'
                $class = $request->getParam('class'
                if ($pageId && $componentKey)
                    $this->_actionController->id = arra
                        'page_id' => $pageI
                        'component_key' => $componentK
                    
                } else
                    $this->_actionController->id = nul
               
                $this->_actionController->class = $clas
                $this->_actionController->pageId = $pageI
                $this->_actionController->componentKey = $componentKe
              

                $id = $this->getRequest()->getParam('componentId'
                $pageCollection = Vps_PageCollection_TreeBase::getInstance(
                $component = $pageCollection->findComponent($id
                if (!$component)
                    $class = $this->getRequest()->getParam('class'
                    Zend_Loader::loadClass($class
                    $component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), $class, $id
               
                if (!$component)
                    throw new Vpc_Exception('Component not found: ' . $id
                } else
                    $this->_actionController->component = $componen
               
                
           

       

   

    public function postDispatch
   
        if (!$this->_noRend
            && (null !== $this->_actionControlle
            && $this->getRequest()->isDispatched
            && !$this->getResponse()->isRedirect(
       
            if ($this->isJson())
                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                    $this->getResponse()->setHeader('Content-Type', 'text/html'
                    $this->getResponse()->setBody($this->view->render('')
                } else if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
                    $this->getResponse()->setHeader('Content-Type', 'text/javascript'
                    $this->getResponse()->setBody($this->view->render('')
                } else
                    echo '<pre>
                    print_r($this->view->getOutput()
                    echo '</pre>
                    die(
               
            } else
                $this->getResponse()->setHeader('Content-Type', 'text/html; charset=utf-8'
                $this->getResponse()->appendBody($this->view->render('')
           
       
   

    public function isJson
   
        $prefix = substr($this->getRequest()->getActionName(), 0, 4
        return ($prefix == 'ajax' || $prefix == 'json'
   

