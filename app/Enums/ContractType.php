<?php

namespace App\Enums;

enum ContractType: string
{
    case FULL_TIME = 'full-time';
    case PART_TIME = 'part-time';
    case CONTRACTOR = 'contractor';
}
