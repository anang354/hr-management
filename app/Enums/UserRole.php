<?php
namespace App\Enums;

enum UserRole: string {
    case Leader     = 'leader';
    case HR_All     = 'hr_all';
    case HR         = 'hr';
    case Manager    = 'manager';
    case Admin      = 'admin';
    case User       = 'user';

    public function getLabel(): ?string
    {
        return match($this) {
            self::Leader     => __('users.user_role.leader'),
            self::HR_All     => __('users.user_role.hr_all'),
            self::Manager    => __('users.user_role.manager'),
            self::Admin      => __('users.user_role.admin'),
            self::HR         => __('users.user_role.hr'),
            self::User       => __('users.user_role.user'),
        };
    }
}
