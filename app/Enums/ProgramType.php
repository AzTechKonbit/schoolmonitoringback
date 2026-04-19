<?php

namespace App\Enums;

enum ProgramType: string
{
    case MASTER = 'master';
    case BACHELOR = 'bachelor';
    case CERTIFICATION = 'certification';
    case DOCTORAT = 'doctorat';
}
