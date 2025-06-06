<?php
/** User: Mohammed ...*/

namespace App\Http\Enums;

enum GroupUserStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';

    case REJECTED = 'rejected';
}