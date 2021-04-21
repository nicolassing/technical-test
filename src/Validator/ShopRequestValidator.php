<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validation;

class ShopRequestValidator
{
    /**
     * @param array<string, mixed> $request
     */
    public function validate(array $request): ConstraintViolationListInterface
    {
        $validator = Validation::createValidator();

        return $validator->validate($request, $this->getConstraint($request));
    }

    /**
     * @param array<string, mixed> $request
     */
    protected function getConstraint(array $request): Assert\Collection
    {
        return new Assert\Collection([
            'q' => new Assert\Type('string'),
            'lat' => [
                new Assert\Type('float'),
                new Assert\Range(['min' => -90, 'max' => 90]),
                new Assert\Callback(
                    function ($data, ExecutionContextInterface $context, $payload) use ($request) {
                        if ((null === $request['lat'] || null === $request['lon']) && (null !== $request['lat'] || null !== $request['lon'])) {
                            $context->buildViolation('You must provide latitude and longitude')
                                ->addViolation();
                        }
                    }
                ),
            ],
            'lon' => [
                new Assert\Type('float'),
                new Assert\Range(['min' => -180, 'max' => 180]),
            ],
            'distance' => [
                new Assert\Type('int'),
                new Assert\Positive(),
            ],
        ]);
    }
}
