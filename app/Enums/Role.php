<?php

namespace App\Enums;

enum Role: string
{
    case Admin    = 'Admin';
    case Manager  = 'Manager';
    case Employee = 'Employee';
}
