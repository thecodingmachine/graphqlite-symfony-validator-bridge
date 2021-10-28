<?php

namespace TheCodingMachine\GraphQLite\Validator;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationFailedExceptionTest extends TestCase
{

    public function testGetExceptions()
    {
        $constraintViolationList = new ConstraintViolationList([
            new ConstraintViolation('foo', 'foo {bar}', ['bar' => 'baz'], null, null, 'invalidValue')
        ]);

        $validationFailedException = new ValidationFailedException($constraintViolationList);

        $this->assertTrue($validationFailedException->hasExceptions());

        $exceptions = $validationFailedException->getExceptions();
        $this->assertCount(1, $exceptions);
        $this->assertSame('foo', $exceptions[0]->getMessage());
    }

    public function testThrowException()
    {
        $constraintViolationList = new ConstraintViolationList([]);

        ValidationFailedException::throwException($constraintViolationList);

        $constraintViolationList = new ConstraintViolationList([
            new ConstraintViolation('foo', 'foo {bar}', ['bar' => 'baz'], null, null, 'invalidValue')
        ]);

        $this->expectException(ValidationFailedException::class);
        ValidationFailedException::throwException($constraintViolationList);
    }
}
