<?php

namespace App\Helper;

use App\Entity\Status;

class StatusCalculator
{
    static function getOverallStatus(Status ...$statuses): Status
    {
        $haveNotDone = false;
        $haveFailed = false;
        $haveNotNew = false;

        foreach ($statuses as $status) {
            if (Status::Done !== $status) {
                $haveNotDone = true;
            }
            if (Status::New !== $status) {
                $haveNotNew = true;
            }
            if (Status::Failed === $status) {
                $haveFailed = true;
            }
        }

        if (!$haveNotDone) {
            return Status::Done;
        }

        if (!$haveNotNew) {
            return Status::New;
        }

        if ($haveFailed) {
            return Status::Failed;
        }

        return Status::Pending;
    }
}