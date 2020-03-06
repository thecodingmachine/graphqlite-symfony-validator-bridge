<?php

declare(strict_types=1);

namespace TheCodingMachine\Graphqlite\Validator;

use GraphQL\Error\ClientAware;
use InvalidArgumentException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use TheCodingMachine\GraphQLite\Exceptions\GraphQLAggregateExceptionInterface;
use Throwable;

class ValidationFailedException extends InvalidArgumentException implements GraphQLAggregateExceptionInterface
{
    /** @var ConstraintViolationException[] */
    private $exceptions = [];

    /**
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $constraintViolationList
     */
    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        parent::__construct('Validation failed:', 400);
        foreach ($constraintViolationList as $constraintViolation) {
            $this->add($constraintViolation);
        }
    }

    private function add(ConstraintViolationInterface $violation): void
    {
        $this->exceptions[] = new ConstraintViolationException($violation);
        $this->message .= "\n" . $violation->getMessage();
    }

    /**
     * @return (ClientAware&Throwable)[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    public function hasExceptions(): bool
    {
        return ! empty($this->exceptions);
    }

    /**
     * Throw the exceptions passed in parameter.
     * If only one exception is passed, it is thrown.
     * If many exceptions are passed, they are bundled in the GraphQLAggregateException
     *
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $constraintViolationList
     *
     * @throws ValidationFailedException
     */
    public static function throwException(ConstraintViolationListInterface $constraintViolationList): void
    {
        if ($constraintViolationList->count() > 0) {
            throw new self($constraintViolationList);
        }
    }
}
