<?php
class Vps_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
    /**
     * Name of layout script to render. Defaults to 'master.html'.
     *
     * @var string
     */
    protected $_layoutScript = 'master.html';

    /**
     * Set the layout script to be rendered.
     *
     * @param string $script
     */
    public function setLayoutScript($script)
    {
        $this->_layoutScript = $script;
    }
    
    /**
     * Retreive the name of the layout script to be rendered.
     *
     * @return string
     */
    public function getLayoutScript()
    {
        return $this->_layoutScript;
    }
    
    /**
     * Render the action script and assign the the view for use
     * in the layout script. Render the layout script and append
     * to the Response's body.
     *
     * @param string $script
     * @param string $name
     */
    public function renderScript($script, $name = null)
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        if (null === $name) {
            $name = $this->getResponseSegment();
        }

        // assign action script name to view.
        $this->view->actionScript = $script;

        // render layout script and append to Response's body
        $layoutScript = $this->getLayoutScript();        
        $layoutContent = $this->view->render($layoutScript);
        $this->getResponse()->appendBody($layoutContent, $name);

        $this->setNoRender();
    }
}
