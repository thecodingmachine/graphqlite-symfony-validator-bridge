<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Annotations;

use Attribute;
use BadMethodCallException;
use Symfony\Component\Validator\Constraint;
use TheCodingMachine\GraphQLite\Annotations\ParameterAnnotationInterface;

use function is_array;
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
#[Attribute(Attribute::TARGET_METHOD)]
class Assertion implements ParameterAnnotationInterface
{
    private string $for;
    /** @var Constraint[] */
    private array $constraint;

    /**
     * @param array<string, mixed> $values
     * @param Constraint[]|Constraint|null $constraint
     */
    public function __construct(
        array $values = [],
        string|null $for = null,
        array|Constraint|null $constraint = null,
    ) {
        $for = $for ?? $values['for'] ?? null;
        $constraint = $constraint ?? $values['constraint'] ?? null;
        if ($for === null) {
            throw new BadMethodCallException('The Assert attribute must be passed a target. For instance: "#[Assert(for: "$email", constraint: new Email())"');
        }

        if ($constraint === null) {
            throw new BadMethodCallException('The Assert attribute must be passed one or many constraints. For instance: "#[Assert(for: "$email", constraint: new Email())"');
        }

        $this->for = ltrim($for, '$');
        $this->constraint = is_array($constraint) ? $constraint : [$constraint];
    }

    public function getTarget(): string
    {
        return $this->for;
    }

    /** @return Constraint[] */
    public function getConstraint(): array
    {
        return $this->constraint;
    }
}
