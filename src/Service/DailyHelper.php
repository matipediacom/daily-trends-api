<?php

namespace App\Service;

use DateTime;

class DailyHelper
{
    public function today(): DateTime
    {
        return new DateTime('today');
    }

    public function tomorrow(): DateTime
    {
        return new DateTime('tomorrow');
    }
}
