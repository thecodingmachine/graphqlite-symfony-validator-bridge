<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Annotations;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;

class AssertionTest extends TestCase
{
    public function testException2(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('The Assertion attribute must be passed one or many constraints. For instance: "#[Assertion(constraint: new Email())"');
        new Assertion(['for' => 'foo']);
    }
}
