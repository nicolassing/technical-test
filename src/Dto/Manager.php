<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class Manager
{
    /**
     * @Groups({"manager:read"})
     */
    public ?int $id = null;

    /**
     * @Groups({"manager:read"})
     */
    #[Assert\NotBlank]
    public ?string $firstname = null;

    /**
     * @Groups({"manager:read"})
     */
    #[Assert\NotBlank]
    public ?string $lastname = null;
}
