<?php

namespace App\Enums;

enum PackageStatus: string
{
    case ARRIVE_CN = 'arrive_cn';
    case IN_TRANSIT = 'in_transit';
    case ARRIVE_MG = 'arrive_mg';
    case RECUPERATED = 'recuperated';
    case RETURNED = 'returned';
}
