<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Mappers\Parameters;

use Exception;
use ReflectionParameter;

class InvalidAssertionAnnotationException extends Exception
{
    public static function canOnlyValidateInputType(ReflectionParameter $refParameter): self
    {
        $class = $refParameter->getDeclaringClass();
        $className = $class ? $class->getName() : '';
        $method = $refParameter->getDeclaringFunction()->getName();

        return new self('In method ' . $className . '::' . $method . '(), the @Assert annotation is targeting parameter "$' . $refParameter->getName() . '". You cannot target this parameter because it is not part of the GraphQL Input type. You can only assert parameters coming from the end user.');
    }
}
