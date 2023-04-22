<?php

namespace App\Entity;

trait SoftDeleteTrait
{
    #[ORM\Column]
    private ?bool $deleted = false;

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}