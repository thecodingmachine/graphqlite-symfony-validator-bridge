<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Annotations;

use Attribute;
use BadMethodCallException;
use Symfony\Component\Validator\Constraint;
use TheCodingMachine\GraphQLite\Annotations\ParameterAnnotationInterface;

use function is_array;
use function ltrim;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Use this annotation to validate a parameter for a query or mutation.
 *
 * Note 1: using this attribute as a target to the method (not parameter) is deprecated and will be removed in 8.0.
 * Note 2: support for `doctrine/annotations` will be removed in 8.0.
 * Note 3: this class won't implement `ParameterAnnotationInterface` in 8.0.
 *
 * @Annotation
 * @Target({"METHOD"})
 * @Attributes({
 *   @Attribute("for", type = "string"),
 *   @Attribute("constraint", type = "Symfony\Component\Validator\Constraint[]|Symfony\Component\Validator\Constraint")
 * })
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_PARAMETER)]
class Assertion implements ParameterAnnotationInterface
{
    private string|null $for = null;
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

        if ($constraint === null) {
            throw new BadMethodCallException('The Assertion attribute must be passed one or many constraints. For instance: "#[Assertion(constraint: new Email())"');
        }

        $this->constraint = is_array($constraint) ? $constraint : [$constraint];

        if ($for === null) {
            return;
        }

        trigger_error(
            "Using #[Assertion(for='" . $for . "', constaint='...')] on methods is deprecated in favor " .
            "of #[Assertion(constraint='...')] the parameter itself.",
            E_USER_DEPRECATED,
        );

        $this->for = ltrim($for, '$');
    }

    public function getTarget(): string
    {
        if ($this->for === null) {
            throw new BadMethodCallException('The Assertion attribute must be passed a target. For instance: "#[Assertion(for: "$email", constraint: new Email())"');
        }

        return $this->for;
    }

    /** @return Constraint[] */
    public function getConstraint(): array
    {
        return $this->constraint;
    }
}
