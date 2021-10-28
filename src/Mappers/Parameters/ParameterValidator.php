<?php

declare(strict_types=1);

namespace TheCodingMachine\GraphQLite\Validator\Mappers\Parameters;

use GraphQL\Type\Definition\InputType;
use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use TheCodingMachine\GraphQLite\Parameters\InputTypeParameterInterface;
use TheCodingMachine\GraphQLite\Validator\ValidationFailedException;

class ParameterValidator implements InputTypeParameterInterface
{
    /** @var InputTypeParameterInterface */
    private $parameter;
    /** @var string */
    private $parameterName;
    /** @var array|Constraint[] */
    private $constraints;
    /** @var ConstraintValidatorFactoryInterface */
    private $constraintValidatorFactory;
    /** @var ValidatorInterface */
    private $validator;
    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param Constraint[] $constraints
     */
    public function __construct(InputTypeParameterInterface $parameter, string $parameterName, array $constraints, ConstraintValidatorFactoryInterface $constraintValidatorFactory, ValidatorInterface $validator, TranslatorInterface $translator)
    {
        $this->parameter = $parameter;
        $this->parameterName = $parameterName;
        $this->constraints = $constraints;
        $this->constraintValidatorFactory = $constraintValidatorFactory;
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * @param array<string, mixed> $args
     * @param mixed $context
     *
     * @return mixed
     */
    public function resolve(?object $source, array $args, $context, ResolveInfo $info)
    {
        $value = $this->parameter->resolve($source, $args, $context, $info);

        $executionContext = new ExecutionContext($this->validator, $this->parameterName, $this->translator, null);

        foreach ($this->constraints as $constraint) {
            $validator = $this->constraintValidatorFactory->getInstance($constraint);
            $validator->initialize($executionContext);
            $executionContext->setConstraint($constraint);
            $executionContext->setNode($value, $source, null, $this->parameterName);
            $validator->validate($value, $constraint);
        }

        $violations = $executionContext->getViolations();

        ValidationFailedException::throwException($violations);

        return $value;
    }

    public function getType(): InputType
    {
        return $this->parameter->getType();
    }

    public function hasDefaultValue(): bool
    {
        return $this->parameter->hasDefaultValue();
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->parameter->getDefaultValue();
    }
}
