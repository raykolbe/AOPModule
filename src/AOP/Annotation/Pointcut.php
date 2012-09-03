<?php

namespace AOP\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Pointcut
{
    /** @var array */
    public $rule = array();
}