<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Entity\Manager;
use App\Repository\ORM\ManagerRepository;
use App\Validator\ManagerExists;
use App\Validator\ManagerExistsValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ManagerExistsValidatorTest extends TestCase
{
    protected ManagerRepository | MockObject $managerRepository;
    protected ManagerExistsValidator $validator;
    protected ExecutionContextInterface | MockObject $context;

    protected function setUp(): void
    {
        $this->managerRepository = $this->createMock(ManagerRepository::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ManagerExistsValidator($this->managerRepository);
        $this->validator->initialize($this->context);
    }

    public function testValidate(): void
    {
        $this->managerRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn(new Manager());
        $this->validator->validate(123, new ManagerExists());
    }

    public function testValidateWithInvalidValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate('123', new ManagerExists());
    }

    public function testValidateWithNotFoundManager(): void
    {
        $this->managerRepository->expects(self::once())
            ->method('find')
            ->with(123)
            ->willReturn(null);
        $violation = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violation->method('setParameter')->willReturn($violation);
        $violation->expects(self::once())->method('addViolation');
        $this->context->expects(self::once())
            ->method('buildViolation')
            ->willReturn($violation);
        $this->validator->validate(123, new ManagerExists());
    }
}
