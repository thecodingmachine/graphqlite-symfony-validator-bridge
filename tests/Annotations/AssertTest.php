<?php

namespace TheCodingMachine\Graphqlite\Validator\Annotations;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;

class AssertTest extends TestCase
{

    public function testException1()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('The @Assert annotation must be passed a target. For instance: "@Assert(for="$email", constraint=@Email)"');
        new Assert([]);
    }

    public function testException2()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('The @Assert annotation must be passed one or many constraints. For instance: "@Assert(for="$email", constraint=@Email)"');
        new Assert(['for'=>'foo']);
    }
}
