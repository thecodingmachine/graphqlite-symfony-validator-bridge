<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Annotations;

use BadMethodCallException;
use Symfony\Component\Validator\Constraint;
use TheCodingMachine\GraphQLite\Annotations\ParameterAnnotationInterface;

use function assert;
use function is_array;
use function is_string;
use function ltrim;

/**
 * Use this annotation to validate a parameter for a query or mutation.
 *
 * @Annotation
 * @Target({"METHOD"})
 * @Attributes({
 *   @Attribute("for", type = "string"),
 *   @Attribute("constraint", type = "Symfony\Component\Validator\Constraint[]|Symfony\Component\Validator\Constraint")
 * })
 */
class Assertion implements ParameterAnnotationInterface
{
    /** @var string */
    private $for;
    /** @var Constraint[] */
    private $constraint;

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values)
    {
        if (! isset($values['for'])) {
            throw new BadMethodCallException('The @Assert annotation must be passed a target. For instance: "@Assert(for="$email", constraint=@Email)"');
        }

        if (! isset($values['constraint'])) {
            throw new BadMethodCallException('The @Assert annotation must be passed one or many constraints. For instance: "@Assert(for="$email", constraint=@Email)"');
        }

        assert(is_string($values['for']));
        $for = ltrim($values['for'], '$');

        $constraints = [];
        if (is_array($values['constraint'])) {
            foreach ($values['constraint'] as $constraint) {
                assert($constraint instanceof Constraint);
                $constraints[] = $constraint;
            }
        } else {
            assert($values['constraint'] instanceof Constraint);
            $constraints = [$values['constraint']];
        }

        $this->for = $for;
        $this->constraint = $constraints;
    }

    public function getTarget(): string
    {
        return $this->for;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraint(): array
    {
        return $this->constraint;
    }
}
