<?php

namespace App\Entity;

enum Status: int
{
    case New = 1;
    case Pending = 2;
    case Failed = 3;
    case Done = 4;
}
