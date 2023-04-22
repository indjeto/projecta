<?php

namespace App\Entity;

interface SoftDeletableInterface
{
    public function setDeleted(bool $deleted);
}