<?php

namespace App\Processor;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @inheritdoc
     */
    public function preProcess(string $fixtureId, $data): void
    {
        if ($data instanceof User) {
            $violations = $this->validator->validate($data);
            if (count($violations) > 0) {
                // Handle validation errors
                $this->handleValidationErrors($violations);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function postProcess(string $fixtureId, $data): void
    {
        // do nothing
    }

    /**
     * Handle validation errors by throwing an exception.
     *
     * @param ConstraintViolationListInterface $violations
     */
    private function handleValidationErrors(ConstraintViolationListInterface $violations): void
    {
        $errors = [];
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $message = $violation->getMessage();
            $errors[] = sprintf('%s: %s', $propertyPath, $message);
        }

        throw new ValidationException($violations);
    }
}
