<?php
namespace KwfBundle\Rest;

use Nelmio\ApiDocBundle\DataTypes;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Route;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Regex;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class ApiDocExtractor implements HandlerInterface
{
    public function handle(ApiDoc $annotation, array $annotations, Route $route, \ReflectionMethod $method)
    {
        if ($method->name == 'cgetAction' && (is_subclass_of($method->class, 'KwfBundle\Controller\FOSRestModelController') || $method->class == 'KwfBundle\Controller\FOSRestModelController')) {
            $annotation->addParameter('limit', array(
                'required' => true,
                'dataType' => 'int',
                'default' => '25'
            ));
            $annotation->addParameter('start', array(
                'required' => true,
                'dataType' => 'int',
                'default' => '0'
            ));
            $annotation->addParameter('sort', array(
                'required' => false,
                'dataType' => 'string',
                'default' => ''
            ));
            $annotation->addParameter('query', array(
                'required' => false,
                'dataType' => 'string',
                'default' => null
            ));
            $annotation->addParameter('filter', array(
                'required' => false,
                'dataType' => 'json string',
                'default' => null
            ));
        }
    }
}
