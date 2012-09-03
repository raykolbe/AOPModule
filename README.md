AOPModule
============

An Aspect Oriented Programming (AOP) Module for Zend Framework 2.

This AOP module wraps the PHP PECL extension [AOP](https://github.com/AOP-PHP/AOP) into Zend Framework 2. If you're not familiar with AOP, take some time to read up on [AspectJ](http://www.eclipse.org/aspectj/doc/next/progguide/index.html) (the Java implementation) and the [PHP PECL extension AOP documentation](https://github.com/AOP-PHP/AOP).

## Requirements
  - [Zend Framework 2](http://www.github.com/zendframework/zf2)
  - [PHP AOP Extension](https://github.com/AOP-PHP/AOP)
  - [Doctrine Common](https://github.com/doctrine/common)

## Installation
Installation of AOPModule uses PHP Composer. For more information about PHP Composer, please visit the official [PHP Composer site](http://getcomposer.org/).

#### Installation steps

  1. `cd my/project/directory`
  2. create a `composer.json` file with following contents:

     ```json
     {
         "minimum-stability" : "dev",
         "require": {
             "dino/aop-module": "dev-master"
         }
     }
     ```
  3. install PHP Composer via `curl -s http://getcomposer.org/installer | php` (on windows, download
     http://getcomposer.org/installer and execute it with PHP)
  4. run `php composer.phar install`
  5. open `my/project/directory/config/application.config.php` and add the following key to your `modules`: 

     ```php
     'AOP',
     ```

#### Configuration Options
The only configuration option is an array of paths to where your aspects (classes) are.

```php
<?php

return array(
    'aop' => array(
        'aspect_class_paths' => array(
            __DIR__ . '/../src/' . __NAMESPACE__ . '/Aspect'
        )
    )
);
```

An aspect looks like this:

```php
<?php

namespace Application\Aspect;

use AOP\Annotation\Pointcut;

class Security
{
    /**
     * The pointcut rule can be a standalone rule or an array of rules,
     * denoted by the curly braces.
     * 
     * @Pointcut(rule={
     *     "before Application\Controller\IndexController->*Action()",
     *     "before Application\Controller\AdminController->*Action()"
     * })
     */
    public function checkActionPrecondition(\AOPTriggeredJoinPoint $triggeredJoinPoint)
    {
        error_log("Check Access Precondition!");
    }

    /**
     * Take note that the rule is not in array notation.
     * 
     * @Pointcut(rule="before Application\Controller\IndexController->*Action()")
     */
    public function checkFooBarPrecondition(\AOPTriggeredJoinPoint $triggeredJoinPoint)
    {
        error_log("Check Foo Bar Precondition!");
    }

    /**
     * @Pointcut(rule="after Application\Controller\IndexController->*Action()")
     */
    public function logActionDispatched(\AOPTriggeredJoinPoint $triggeredJoinPoint)
    {
        /**
         * If ServiceLocatorAwareInterface was implemented, we could call:
         *
         * $this->getServiceLocator()
         *      ->get('logger')
         *      ->info('We dispatched an action.');
         */

        error_log("My logging advice!");
    }
}
```

## Rule syntax
The syntax follows that of the AOP PECL extension with the exception of the prepended "before", "after", or "around" keywords to the rule.

## Notes
  - If your aspect implements `Zend\ServiceManager\ServiceLocatorAwareInterface`, the ServiceManager instance on Application will be injected.
  - This module is set up as soon as ZF2 allows (MVC Bootstrap) and at the highest priority on the event stack.
  - v0.1.0 of AOP is currently supported. Future releases promise a more robust lexicon.