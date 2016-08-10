<?php
namespace KwfBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use KwfBundle\Rest\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use KwfBundle\Rest\Annotations\Query;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Context\Context;

class FOSRestModelController extends Controller implements ClassResourceInterface
{
    protected $_model;
    protected $_queryColumns;
    protected $_querySplit = false;

    public function __construct()
    {
        if (is_string($this->_model)) $this->_model = \Kwf_Model_Abstract::getInstance($this->_model);
    }

    protected function _getSelect(ParamFetcher $paramFetcher, Request $request)
    {
        $select = new \Kwf_Model_Select();
        $select->limit($paramFetcher->get('limit'), $paramFetcher->get('start'));

        $queryValue = trim($request->get('query'));
        if ($queryValue) {

            $exprs = array();
            if (!$this->_queryColumns) {
                throw new \Kwf_Exception("_queryColumns are required to be set");
            }

            if ($this->_querySplit) {
                $queryValue = explode(' ', $queryValue);
            } else {
                $queryValue = array($queryValue);
            }

            foreach ($queryValue as $q) {
                $e = array();
                foreach ($this->_queryColumns as $c) {
                    $e[] = new \Kwf_Model_Select_Expr_Like($c, '%'.$q.'%');
                }

                if (count($e) > 1) {
                    $exprs[] = new \Kwf_Model_Select_Expr_Or($e);
                } else {
                    $exprs[] = $e[0];
                }
            }

            if (count($exprs) > 1) {
                $select->where(new \Kwf_Model_Select_Expr_And($exprs));
            } else {
                $select->where($exprs[0]);
            }
        }

        return $select;
    }

    /**
     * @ApiDoc()
     */
    public function cgetAction(ParamFetcher $paramFetcher, Request $request)
    {
        $dynamicRequestParam = new QueryParam();
        $dynamicRequestParam->name = "limit";
        $dynamicRequestParam->requirements = "\d+";
        $dynamicRequestParam->default = '25';
        $paramFetcher->addParam($dynamicRequestParam);

        $dynamicRequestParam = new QueryParam();
        $dynamicRequestParam->name = "start";
        $dynamicRequestParam->requirements = "\d+";
        $dynamicRequestParam->default = '0';
        $paramFetcher->addParam($dynamicRequestParam);

        $dynamicRequestParam = new QueryParam();
        $dynamicRequestParam->name = "sort";
        $dynamicRequestParam->requirements = "[\w_]+";
        $dynamicRequestParam->default = null;
        $paramFetcher->addParam($dynamicRequestParam);

        $dynamicRequestParam = new QueryParam();
        $dynamicRequestParam->name = "filter";
        $dynamicRequestParam->default = null;
        $paramFetcher->addParam($dynamicRequestParam);

        $dynamicRequestParam = new QueryParam();
        $dynamicRequestParam->name = "query";
        $dynamicRequestParam->default = null;
        $paramFetcher->addParam($dynamicRequestParam);

        $select = $this->_getSelect($paramFetcher, $request);

        $restColumns = array_keys($this->_model->getSerializationColumns(array('rest_read', 'rest')));
        $exprColumns = $this->_model->getExprColumns();
        foreach (array_intersect($restColumns, $exprColumns) as $i) {
            $select->expr($i);
        }

        $rows = $this->_model->getRows($select);
        foreach ($rows as $row) {
            $this->denyAccessUnlessGranted('read', $row);
        }
        $view = View::create();
        $view->setData(array(
            'data'=>$rows,
            'total' => $this->_model->countRows($select)
        ));
        $ctx = new Context();
        $ctx->setGroups(array('rest_read', 'rest'));
        $view->setContext($ctx);
        return $view;
    }

    /**
     * @ApiDoc()
     */
    public function getAction($id)
    {
        $row = $this->_model->getRow($id);

        if (!$row) {
            $view = View::create(array(), Codes::HTTP_NOT_FOUND);
            $ctx = new Context();
            $view->setContext($ctx);
            return $view;
        }
        $this->denyAccessUnlessGranted('read', $row);

        $view = View::create(array(
            'data'=>$row
        ));
        $ctx = new Context();
        $ctx->setGroups(array('rest_read', 'rest'));
        $view->setContext($ctx);
        return $view;
    }

    /**
     * @ApiDoc()
     */
    public function postAction(ParamFetcher $paramFetcher, Request $request)
    {
        $row = $this->_model->createRow();
        $this->get('serializer')->denormalize($request->request->all(), get_class($row),
                                              'json',
                                              array('object_to_populate'=>$row,
                                                    'groups'=>array('rest_write', 'rest')));
        $validator = $this->get('validator');
        $errors = $validator->validate($row);
        if (count($errors)) {
            $formattedErrors = array();
            foreach ($errors as $error) {
                $formattedErrors[] = array(
                    'message' => $error->getMessage(),
                    'code' => $error->getCode(),
                    'propertyPath' => $error->getPropertyPath()
                );
            }
            $view = View::create(array(
                'errors'=>$formattedErrors
            ), 400);
            $ctx = new Context();
            $view->setContext($ctx);
            return $view;
        } else {
            $this->denyAccessUnlessGranted('create', $row);
            $this->_beforeInsert($row);
            $this->_beforeSave($row);
            $row->save();
            $this->_afterSave($row, $request);
            $this->_afterInsert($row, $request);

            //there must be a better way to do that
            $getRouteName = preg_replace('#^post_(.*)$#', 'get_\1', $request->get('_route'));
            return View::createRedirect($this->generateUrl(
                    $getRouteName,
                    array('id' => $row->id, 'version' => $request->get('version'))
                ),
                Codes::HTTP_CREATED
            );
        }
    }

    /**
     * @ApiDoc()
     */
    public function putAction($id, ParamFetcher $paramFetcher, Request $request)
    {
        $row = $this->_model->getRow($id);
        if (!$row) {
            $view = View::create(array(), Codes::HTTP_NOT_FOUND);
            $ctx = new Context();
            $view->setContext($ctx);
            return $view;
        }

        $this->get('serializer')->denormalize($request->request->all(), get_class($row),
                                              'json',
                                              array('object_to_populate'=>$row,
                                                    'groups'=>array('rest_write', 'rest')));
        $validator = $this->get('validator');
        $errors = $validator->validate($row);
        if (count($errors)) {
            $formattedErrors = array();
            foreach ($errors as $error) {
                $formattedErrors[] = array(
                    'message' => $error->getMessage(),
                    'code' => $error->getCode(),
                    'propertyPath' => $error->getPropertyPath()
                );
            }
            $view = View::create(array(
                'errors'=>$formattedErrors
            ), 400);
            $ctx = new Context();
            $view->setContext($ctx);
            return $view;
        } else {
            $this->denyAccessUnlessGranted('update', $row);
            $this->_beforeUpdate($row);
            $this->_beforeSave($row);
            $row->save();
            $this->_afterSave($row, $request);
            $this->_afterUpdate($row, $request);

            $view = View::create(array(), Codes::HTTP_NO_CONTENT);
            $ctx = new Context();
            $view->setContext($ctx);
            return $view;
        }

    }

    /**
     * @ApiDoc()
     */
    public function deleteAction($id)
    {
        $row = $this->_model->getRow($id);
        if (!$row) {
            $view = View::create(array(), Codes::HTTP_NOT_FOUND);
            $ctx = new Context();
            $view->setContext($ctx);
            return $view;
        }
        $this->denyAccessUnlessGranted('delete', $row);
        $this->_beforeDelete($row);
        $row->delete();
        $view =  View::create(array(), Codes::HTTP_NO_CONTENT);
        $ctx = new Context();
        $view->setContext($ctx);
        return $view;
    }

    protected function _beforeInsert(\Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeUpdate(\Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeSave(\Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeDelete(\Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterUpdate(\Kwf_Model_Row_Interface $row, Request $request)
    {
    }

    protected function _afterInsert(\Kwf_Model_Row_Interface $row, Request $request)
    {
    }

    protected function _afterSave(\Kwf_Model_Row_Interface $row, Request $request)
    {
    }
}
