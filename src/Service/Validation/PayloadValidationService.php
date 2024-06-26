<?php

declare(strict_types=1);

namespace App\Service\Validation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PayloadValidationService extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator
    )
    {
    }

    public function validatePayload(object $entity, array $constraints = null, string $field = null): ?array
    {
        $errors = [];
        $validationErrors = $this->validator->validate($entity, $constraints);

        if (count($validationErrors) > 0) {
            foreach ($validationErrors as $error) {
                $errors[] = [
                    'field' => $field ?? $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }

            return $errors;
        }

        return null;
    }

}