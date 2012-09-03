<?php

namespace AOP\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Pointcut
{
    public $rule = null;
}