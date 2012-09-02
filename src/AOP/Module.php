<?php

namespace AOP;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\StaticEventManager;

class Module
{
    public function __construct()
    {
        $manager = StaticEventManager::getInstance();
        $manager->attach('Zend\Mvc\Application', MvcEvent::EVENT_BOOTSTRAP, array($this, 'registerAdvices'), PHP_INT_MAX);
    }
    
    public function registerAdvices(MvcEvent $event)
    {
        $application = $event->getApplication();
        $configuration = $application->getConfig();
        
        $beforeJoins = isset($configuration['aop']['before']) ? $configuration['aop']['before'] : array();
        $afterJoins = isset($configuration['aop']['after']) ? $configuration['aop']['after'] : array();
        $aroundJoins = isset($configuration['aop']['around']) ? $configuration['aop']['around'] : array();
        
        foreach ($beforeJoins as $pointcut => $advice) {
            // @todo Make sure advice is callable
            aop_add_before($pointcut, $advice);
        }
        
        foreach ($afterJoins as $pointcut => $advice) {
            // @todo Make sure advice is callable
            aop_add_after($pointcut, $advice);
        }
        
        foreach ($aroundJoins as $pointcut => $advice) {
            // @todo Make sure advice is callable
            aop_add_around($pointcut, $advice);
        }
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
