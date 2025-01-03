<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Fixtures\Controllers;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Validator\Annotations\Assertion;
use TheCodingMachine\GraphQLite\Validator\Fixtures\Types\User;
use TheCodingMachine\GraphQLite\Validator\ValidationFailedException;

class UserController
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    #[Mutation]
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

    #[Query]
    #[Assertion(for: '$email', constraint: new Assert\Email())]
    public function findByMailTargetMethod(string $email = 'a@a.com'): User
    {
        // deprecated way of using the Assertion annotation - as a target of the method
        return new User($email, 'foo');
    }

    #[Query]
    public function findByMail(
        #[Assertion(constraint: new Assert\Email())]
        string $email = 'a@a.com',
    ): User {
        return new User($email, 'foo');
    }
}
