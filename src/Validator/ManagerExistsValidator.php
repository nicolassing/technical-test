<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\ORM\ManagerRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ManagerExistsValidator extends ConstraintValidator
{
    protected ManagerRepository $managerRepository;

    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ManagerExists) {
            throw new UnexpectedTypeException($constraint, ManagerExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_int($value)) {
            throw new UnexpectedValueException($value, 'int');
        }

        $manager = $this->managerRepository->find($value);

        if (null === $manager) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ id }}', (string) $value)
                ->addViolation();
        }
    }
}
