<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

trait SoftDeleteTrait
{
    #[ORM\Column(nullable: true)]
    #[Ignore]
    private ?\DateTimeImmutable $deletedAt = null;
    #[Ignore]
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
    #[Ignore]
    public function setDeleted(bool $deleted): void
    {
        $this->deletedAt = $deleted ? new \DateTimeImmutable() : null;
    }


}