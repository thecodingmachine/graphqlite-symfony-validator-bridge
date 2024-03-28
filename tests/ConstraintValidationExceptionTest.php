<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;

class ConstraintValidationExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new ConstraintViolationException(new ConstraintViolation('foo', 'foo {bar}', ['bar' => 'baz'], null, null, 'invalidValue', null, 'myCode'));
        $this->assertSame(400, $exception->getCode());
        $this->assertTrue($exception->isClientSafe());
        $this->assertSame(['category' => 'Validate', 'code' => 'myCode'], $exception->getExtensions());

        $exception = new ConstraintViolationException(new ConstraintViolation('foo', 'foo {bar}', ['bar' => 'baz'], null, null, 'invalidValue'));
        $this->assertSame(['category' => 'Validate'], $exception->getExtensions());
    }
}
