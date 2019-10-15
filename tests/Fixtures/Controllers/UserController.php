<?php


namespace TheCodingMachine\Graphqlite\Validator\Fixtures\Controllers;


use Symfony\Component\Validator\Constraints as Assertion;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\Graphqlite\Validator\Fixtures\Types\User;
use TheCodingMachine\Graphqlite\Validator\Annotations\Assert;
use TheCodingMachine\Graphqlite\Validator\ValidationFailedException;

class UserController
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @Mutation()
     */
    public function createUser(string $email, string $password): User
    {
        $user = new User($email, $password);

        // Let's validate the user
        $errors = $this->validator->validate($user);

        // Throw an appropriate GraphQL exception if validation errors are encountered
        ValidationFailedException::throwException($errors);

        // No errors? Let's continue and save the user
        return $user;
    }

    /**
     * @Query
     * @Assert(for="email", constraint=@Assertion\Email())
     */
    public function findByMail(string $email = 'a@a.com'): User
    {
        $user = new User($email, 'foo');
        return $user;
    }
}
