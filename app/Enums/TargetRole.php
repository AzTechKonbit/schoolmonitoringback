<?php

namespace App\Enums;

enum TargetRole: string
{
    case ALL = 'all';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case PARENT = 'parent';
}
