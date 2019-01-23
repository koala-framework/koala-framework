<?php
namespace KwfBundle\Rest;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use KwfBundle\Rest\View;
use FOS\RestBundle\Context\Context;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use KwfBundle\Form\KwfModelDataMapper;
use FOS\RestBundle\Util\Codes;
use KwfBundle\Rest\SubmitHandler;

class Handler
{
    protected $model;
    protected $authorizationChecker;
    protected $validator;

    public function __construct(\Kwf_Model_Abstract $model, AuthorizationChecker $authorizationChecker, ValidatorInterface $validator)
    {
        $this->model = $model;
        $this->authorizationChecker = $authorizationChecker;
        $this->validator = $validator;
    }

    public function buildSelect(Request $request, array $options = array())
    {
        $select = new \Kwf_Model_Select();
        if ($request->get('limit')) {
            $select->limit($request->get('limit'), $request->get('start'));
        } else {
            $select->limit(25, $request->get('start'));
        }


        $queryValue = trim($request->get('query'));
        if ($queryValue) {

            $queryColumns = null;
            if (isset($options['queryColumns'])) {
                $queryColumns = $options['queryColumns'];
            }
            $exprs = array();
            if (!$queryColumns) {
                throw new \Kwf_Exception("queryColumns are required to be set");
            }

            if (isset($options['querySplit']) && $options['querySplit']) {
                $queryValue = explode(' ', $queryValue);
            } else {
                $queryValue = array($queryValue);
            }

            foreach ($queryValue as $q) {
                $e = array();
                foreach ($queryColumns as $c) {
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

        $restColumns = $this->model->getSerializationColumns(array('rest_read', 'rest'));
        $exprColumns = $this->model->getExprColumns();
        foreach (array_intersect(array_keys($restColumns), $exprColumns) as $i) {
            $select->expr($i);
        }

        return $select;
    }

    public function buildSubmitHandler(array $context = array(), $serializationColumns = array('rest_write', 'rest'))
    {
        $fields = array();
        $columns = $this->model->getSerializationColumns($serializationColumns);
        foreach ($columns as $column=>$settings) {
            if (isset($settings['type'])) {
                $type = $settings['type'];
            } else {
                $type = 'Column';
            }
            if (substr($type, 0, 1) === '@') {
                $columnNormalizer = \Kwf_Util_Symfony::getKernel()->getContainer()->get(substr($type, 1));
            } else {
                if (!class_exists($type)) {
                    $type = 'KwfBundle\\Serializer\\KwfModel\\ColumnNormalizer\\' . $type;
                }
                $columnNormalizer = new $type;
            }
            $constraints = $columnNormalizer->getValidationConstraints($column, $settings, $context);
            $fields[] = array(
                'name' => $column,
                'columnNormalizer' => $columnNormalizer,
                'constraints' => $constraints,
                'settings' => $settings
            );
        }
        $ret = new SubmitHandler($fields);
        $ret->setValidator($this->validator);
        return $ret;
    }

    public function createView($data = null, $statusCode = null, array $headers = array())
    {
        $view =  View::create($data, $statusCode, $headers);
        $ctx = new Context();
        $ctx->setGroups(array('rest_read', 'rest'));
        $view->setContext($ctx);
        return $view;
    }

    public function createViewRedirectCreated($id, Request $request, Router $router)
    {
        //there must be a better way to do that
        $getRouteName = preg_replace('#^post_(.*)$#', 'get_\1', $request->get('_route'));
        return View::createRedirect($router->generate(
                $getRouteName,
                array('id' => $id, 'version' => $request->get('version'))
            ),
            Codes::HTTP_CREATED
        );
    }

    public function getRowGranted($id, $attributes)
    {
        $row = $this->model->getRow($id);
        if (!$row) {
            throw new NotFoundHttpException('Not Found.');
        }
        if (!$this->authorizationChecker->isGranted($attributes, $row)) {
            throw new AccessDeniedException('Access Denied.');
        }
        return $row;
    }

    public function getRowsGranted($select, $attributes)
    {
        $rows = $this->model->getRows($select);
        foreach ($rows as $row) {
            if (!$this->authorizationChecker->isGranted($attributes, $row)) {
                throw new AccessDeniedException('Access Denied.');
            }
        }
        return $rows;
    }

    public function countRows($select)
    {
        return $this->model->countRows($select);
    }

    public function createRow()
    {
        return $this->model->createRow();
    }

    public function getModel()
    {
        return $this->model;
    }
}
