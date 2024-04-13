<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Annotations;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;

class AssertionTest extends TestCase
{
    public function testException1(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('The Assert attribute must be passed a target. For instance: "#[Assert(for: "$email", constraint: new Email())"');
        new Assertion([]);
    }

    public function testException2(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('The Assert attribute must be passed one or many constraints. For instance: "#[Assert(for: "$email", constraint: new Email())"');
        new Assertion(['for' => 'foo']);
    }
}
