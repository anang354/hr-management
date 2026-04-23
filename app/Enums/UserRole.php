<?php
namespace App\Enums;

enum UserRole: string {
    case Leader     = 'leader';
    case Supervisor = 'supervisor';
    case Manager    = 'manager';
    case Admin      = 'admin';
    case HR         = 'hr';
    case User       = 'user';
}