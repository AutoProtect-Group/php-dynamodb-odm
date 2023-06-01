<?php

declare(strict_types=1);

namespace spec\Autoprotect\DynamodbODM\Annotation;

use Autoprotect\DynamodbODM\Annotation\Property\PropertyInterface;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;

abstract class AbstractAnnotationSpec extends ObjectBehavior
{
    public function getMatchers(): array
    {
        return [
            'haveTheAnnotationsLike' => function ($subject, array $annotationsExpected) {
                if (!is_array($subject)) {
                    throw new FailureException("Should have array type");
                }

                $annotationsActual = [];

                /** @var PropertyInterface $annotationProperty */
                foreach ($subject as $annotationProperty) {
                    $annotationsActual[$annotationProperty->getName()] = $annotationProperty->getType();
                }

                return $annotationsActual === $annotationsExpected;
            },
            'beIdProperty' => function ($subject, $expectedName) {
                if (!$subject instanceof PropertyInterface) {
                    throw new FailureException("Should be instance of %s", PropertyInterface::class);
                }

                if (!$subject->isPrimary()) {
                    throw new FailureException("It should be a primary key");
                }

                return $subject->getName() === $expectedName;
            },
            'beSortKeyProperty' => function ($subject, $expectedName) {
                if (!$subject instanceof PropertyInterface) {
                    throw new FailureException("Should be instance of %s", PropertyInterface::class);
                }

                if (!$subject->isSortKey()) {
                    throw new FailureException("It should be a sort key");
                }

                return $subject->getName() === $expectedName;
            },
        ];
    }
}
