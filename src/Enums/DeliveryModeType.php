<?php

namespace App\Enums;

enum DeliveryModeType: string{
    case EXPRESS = 'EXPRESS';
    case BATTERY = 'BATTERY';
    case NORMAL = 'NORMAL';
    case MARITIME = 'MARITIME';
}