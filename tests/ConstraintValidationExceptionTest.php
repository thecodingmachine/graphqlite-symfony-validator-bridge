<?php

namespace TheCodingMachine\GraphQLite\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;

class ConstraintValidationExceptionTest extends TestCase
{

    public function testException()
    {
        $exception = new ConstraintViolationException(new ConstraintViolation('foo', 'foo {bar}', ['bar' => 'baz'], null, null, 'invalidValue', null, 'myCode'));
        $this->assertSame(400, $exception->getCode());
        $this->assertTrue($exception->isClientSafe());
        $this->assertSame('Validate', $exception->getCategory());
        $this->assertSame(['code' => 'myCode'], $exception->getExtensions());

        $exception = new ConstraintViolationException(new ConstraintViolation('foo', 'foo {bar}', ['bar' => 'baz'], null, null, 'invalidValue'));
        $this->assertSame([], $exception->getExtensions());
    }
}
