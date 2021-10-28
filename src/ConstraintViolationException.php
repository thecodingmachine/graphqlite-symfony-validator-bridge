<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator;

use Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLExceptionInterface;

class ConstraintViolationException extends Exception implements GraphQLExceptionInterface
{
    /** @var ConstraintViolationInterface */
    private $violation;

    public function __construct(ConstraintViolationInterface $violation)
    {
        parent::__construct((string) $violation->getMessage(), 400);
        $this->violation = $violation;
    }

    /**
     * Returns true when exception message is safe to be displayed to a client.
     */
    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * Returns string describing a category of the error.
     *
     * Value "graphql" is reserved for errors produced by query parsing or validation, do not use it.
     */
    public function getCategory(): string
    {
        return 'Validate';
    }

    /**
     * Returns the "extensions" object attached to the GraphQL error.
     *
     * @return array<string, mixed>
     */
    public function getExtensions(): array
    {
        $extensions = [];
        $code = $this->violation->getCode();
        if ($code !== null) {
            $extensions['code'] = $code;
        }

        $propertyPath = $this->violation->getPropertyPath();
        if ($propertyPath !== null) {
            $extensions['field'] = $propertyPath;
        }

        return $extensions;
    }
}
