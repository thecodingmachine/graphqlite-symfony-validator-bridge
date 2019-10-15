<?php


namespace TheCodingMachine\Graphqlite\Validator\Fixtures\InvalidControllers;


use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\Validator\Constraints as Assertion;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assert;
use TheCodingMachine\Graphqlite\Validator\ValidationFailedException;

class InvalidController
{
    /**
     * @Query
     * @Assert(for="$resolveInfo", constraint=@Assertion\Email())
     */
    public function invalid(ResolveInfo $resolveInfo): string
    {
        return 'foo';
    }
}
