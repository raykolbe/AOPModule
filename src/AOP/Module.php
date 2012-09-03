<?php

namespace AOP;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\StaticEventManager;
use Zend\File\ClassFileLocator;
use Doctrine\Common\Annotations;

class Module
{
    public function __construct()
    {
        $manager = StaticEventManager::getInstance();
        $manager->attach(
            'Zend\Mvc\Application',
             MvcEvent::EVENT_BOOTSTRAP,
             array($this, 'registerAspects'),
             PHP_INT_MAX
        );
    }
    
    public function registerAspects(MvcEvent $event)
    {
        $application = $event->getApplication();
        $config = $application->getConfig();
        
        Annotations\AnnotationRegistry::registerAutoloadNamespace(__NAMESPACE__ . '\Annotation');
        
        foreach ($config['aop']['aspect_class_paths'] as $path) {
            foreach (new ClassFileLocator($path) as $classInfoFile) {
                foreach ($classInfoFile->getClasses() as $class) {
                    $aspect = new $class;
                    $reader = new Annotations\AnnotationReader();
                    $reflection = new \ReflectionClass($aspect);
                    
                    foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                        $annotation = $reader->getMethodAnnotation($method, 'AOP\Annotation\Pointcut');
                        $advice = $method->getName();
                        $pointcuts = $annotation->rule;
                        
                        foreach ($pointcuts as $pointcut) {
                            list($trigger, $rule) = sscanf($pointcut, "%s %s");
                            switch ($trigger) {
                                case 'before' :
                                    aop_add_before($rule, array($aspect, $advice));
                                    break;
                                case 'after' :
                                    aop_add_after($rule, array($aspect, $advice));
                                    break;
                                case 'around' :
                                    aop_add_around($rule, array($aspect, $advice));
                                    break;
                            }
                        }
                        
                        if ($aspect instanceof \Zend\ServiceManager\ServiceLocatorAwareInterface) {
                            $aspect->setServiceLocator($application->getServiceManager());
                        }
                    }
                }
            }
        }
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
