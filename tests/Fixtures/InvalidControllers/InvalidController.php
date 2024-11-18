<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Fixtures\InvalidControllers;

use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Validator\Annotations\Assertion;

class InvalidController
{
    #[Query]
    public function invalid(
        #[Assertion(for: '$resolveInfo', constraint: new Assert\Email())]
        ResolveInfo $resolveInfo,
    ): string {
        return 'foo';
    }
}
