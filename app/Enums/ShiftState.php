<?php

namespace App\Enums;

enum ShiftState: string
{
  case Pending = 'pending';
  case InProgress = 'in_progress';
  case Completed = 'completed';
  case Cancelled = 'cancelled';
  case Distracted = 'distracted';
  case Qualified = 'qualified';
}
