<?php
class Kwf_Controller_Action_Media_UploadsController extends Kwf_Rest_Controller_Model
{
    protected $_model;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->_model = Kwf_Config::getValue('uploadsModelClass');
        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        throw new Kwf_Exception_NotFound();
    }

    public function deleteAction()
    {
        throw new Kwf_Exception_NotFound();
    }

    public function putAction()
    {
        throw new Kwf_Exception_NotFound();
    }

    public function postAction()
    {
        if (substr($this->getRequest()->getHeader('Content-Type'), 0, 19) == 'multipart/form-data') {
            if (!isset($_FILES['file'])) {
                throw new Kwf_Exception_NotFound();
            }
            $row = $this->_model->uploadFile($_FILES['file']);

            $this->view->data = $this->_loadDataFromRow($row);

            //don't send json content-type as that doesn't work correctly in IE8 with Flash backend (download appears)
            header("Content-Type: text/html");
            echo json_encode(array(
                'success' => true,
                'data' => $this->view->data
            ));
            exit;
        } else {
            throw new Kwf_Exception_NotFound();
        }
    }

    protected function _loadDataFromRow($row)
    {
        $ret = parent::_loadDataFromRow($row);
        $info = $row->getFileInfo();
        $ret['file_size'] = $info['fileSize'];
        $ret['hash_key'] = $info['hashKey'];
        $ret['image'] = $info['image'];
        if ($info['image']) {
            $ret['image_width'] = $info['imageWidth'];
            $ret['image_height'] = $info['imageHeight'];
        }
        return $ret;
    }
}
