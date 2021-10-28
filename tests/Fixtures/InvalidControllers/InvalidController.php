<?php


namespace TheCodingMachine\GraphQLite\Validator\Fixtures\InvalidControllers;


use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Validator\Annotations\Assertion;

class InvalidController
{
    /**
     * @Query
     * @Assertion(for="$resolveInfo", constraint=@Assert\Email())
     */
    public function invalid(ResolveInfo $resolveInfo): string
    {
        return 'foo';
    }
}
