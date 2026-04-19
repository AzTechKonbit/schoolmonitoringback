<?php

namespace App\Enums;

enum UserRole: string
{
    case PARENT = 'parent';
    case STUDENT = 'student';
    case EMPLOYEE = 'employee';
}
