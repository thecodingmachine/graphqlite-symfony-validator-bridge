<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Fixtures\Types;

use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type]
class User
{
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private string $email;

    /**
     * The NotCompromisedPassword assertion asks the "HaveIBeenPawned" service if your password has already leaked or not.
     */
    #[Assert\Length(min: 8)]
    private string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    #[Field]
    public function getEmail(): string
    {
        return $this->email;
    }
}
