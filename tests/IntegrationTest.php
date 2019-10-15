<?php


namespace TheCodingMachine\Graphqlite\Validator;


use Doctrine\Common\Annotations\AnnotationReader;
use GraphQL\Error\Debug;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Mouf\Picotainer\Picotainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use TheCodingMachine\GraphQLite\Containers\BasicAutoWiringContainer;
use TheCodingMachine\GraphQLite\Containers\EmptyContainer;
use TheCodingMachine\GraphQLite\Exceptions\WebonyxErrorHandler;
use TheCodingMachine\GraphQLite\SchemaFactory;
use TheCodingMachine\Graphqlite\Validator\Fixtures\Controllers\UserController;
use TheCodingMachine\Graphqlite\Validator\Mappers\Parameters\AssertParameterMiddleware;
use TheCodingMachine\Graphqlite\Validator\Mappers\Parameters\InvalidAssertAnnotationException;
use function var_dump;
use function var_export;
use const JSON_PRETTY_PRINT;

class IntegrationTest extends TestCase
{
    private function getSchemaFactory(): SchemaFactory
    {
        $container = new Picotainer([
            TranslatorInterface::class => function(ContainerInterface $container) {
                return new Translator('fr_FR');
            },
            ValidatorInterface::class => function(ContainerInterface $container) {
                $build = new ValidatorBuilder();
                $build->enableAnnotationMapping(new AnnotationReader());
                $build->setTranslator($container->get(TranslatorInterface::class));
                return $build->getValidator();
            },
            UserController::class => function(ContainerInterface $container) {
                return new UserController($container->get(ValidatorInterface::class));
            }
        ]);

        $schemaFactory = new SchemaFactory(new ArrayCache(), new BasicAutoWiringContainer($container));
        $schemaFactory->addControllerNamespace('TheCodingMachine\Graphqlite\Validator\Fixtures\Controllers');
        $schemaFactory->addTypeNamespace('TheCodingMachine\Graphqlite\Validator\Fixtures\Types');
        $schemaFactory->addParameterMiddleware(new AssertParameterMiddleware(new ContainerConstraintValidatorFactory($container), $container->get(ValidatorInterface::class), $container->get(TranslatorInterface::class)));

        return $schemaFactory;
    }

    private function getSchema(): Schema
    {
        return $this->getSchemaFactory()->createSchema();
    }

    public function testEndToEndThrowException(): void
    {
        $schema = $this->getSchema();
        $schema->assertValid();

        $queryString = '
        mutation {
          createUser(email: "foo@fgdjkerbrtehrthjker.com", password: "short")  {
            email
          }
        }
        ';

        $result = GraphQL::executeQuery(
            $schema,
            $queryString
        );
        $result->setErrorsHandler([WebonyxErrorHandler::class, 'errorHandler']);
        $result->setErrorFormatter([WebonyxErrorHandler::class, 'errorFormatter']);

        $errors = $result->toArray(Debug::RETHROW_UNSAFE_EXCEPTIONS)['errors'];
        $this->assertSame('The email \'"foo@fgdjkerbrtehrthjker.com"\' is not a valid email.', $errors[0]['message']);
        $this->assertSame('email', $errors[0]['extensions']['field']);
        $this->assertSame('Validate', $errors[0]['extensions']['category']);
        $this->assertSame('This value is too short. It should have 8 characters or more.', $errors[1]['message']);
        $this->assertSame('password', $errors[1]['extensions']['field']);
        $this->assertSame('Validate', $errors[1]['extensions']['category']);
    }

    public function testEndToEndAssert(): void
    {
        $schema = $this->getSchema();
        $schema->assertValid();

        $queryString = '
        {
          findByMail(email: "notvalid")  {
            email
          }
        }
        ';

        $result = GraphQL::executeQuery(
            $schema,
            $queryString
        );
        $result->setErrorsHandler([WebonyxErrorHandler::class, 'errorHandler']);
        $result->setErrorFormatter([WebonyxErrorHandler::class, 'errorFormatter']);

        $errors = $result->toArray(Debug::RETHROW_UNSAFE_EXCEPTIONS)['errors'];

        // TODO: find why message is not in French...
        $this->assertSame('This value is not a valid email address.', $errors[0]['message']);
        $this->assertSame('email', $errors[0]['extensions']['field']);
        $this->assertSame('Validate', $errors[0]['extensions']['category']);


        $queryString = '
        {
          findByMail(email: "valid@valid.com")  {
            email
          }
        }
        ';

        $result = GraphQL::executeQuery(
            $schema,
            $queryString
        );
        $result->setErrorsHandler([WebonyxErrorHandler::class, 'errorHandler']);
        $result->setErrorFormatter([WebonyxErrorHandler::class, 'errorFormatter']);

        $data = $result->toArray(Debug::RETHROW_UNSAFE_EXCEPTIONS)['data'];
        $this->assertSame('valid@valid.com', $data['findByMail']['email']);

        // Test default parameter
        $queryString = '
        {
          findByMail  {
            email
          }
        }
        ';

        $result = GraphQL::executeQuery(
            $schema,
            $queryString
        );
        $result->setErrorsHandler([WebonyxErrorHandler::class, 'errorHandler']);
        $result->setErrorFormatter([WebonyxErrorHandler::class, 'errorFormatter']);

        $data = $result->toArray(Debug::RETHROW_UNSAFE_EXCEPTIONS)['data'];
        $this->assertSame('a@a.com', $data['findByMail']['email']);

    }

    public function testException(): void
    {
        $schemaFactory = $this->getSchemaFactory();
        $schemaFactory->addControllerNamespace('TheCodingMachine\Graphqlite\Validator\Fixtures\InvalidControllers');
        $schema = $schemaFactory->createSchema();

        $this->expectException(InvalidAssertAnnotationException::class);
        $this->expectExceptionMessage('In method TheCodingMachine\Graphqlite\Validator\Fixtures\InvalidControllers\InvalidController::invalid(), the @Assert annotation is targeting parameter "$resolveInfo". You cannot target this parameter because it is not part of the GraphQL Input type. You can only assert parameters coming from the end user.');
        $schema->validate();
    }
}