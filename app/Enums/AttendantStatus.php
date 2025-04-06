<?php

namespace App\Enums;

enum AttendantStatus: string
{
  case Offline = 'offline';
  case Free = 'free';
  case Busy = 'busy';
  case Absent = 'absent';
}
