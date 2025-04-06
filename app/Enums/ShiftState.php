<?php

namespace App\Enums;

enum ShiftState: string
{
  case Pending = 'pending';
  case PendingTransferred = 'pending-transferred';
  case Transferred = 'transferred';
  case InProgress = 'in_progress';
  case Completed = 'completed';
  case Cancelled = 'cancelled';
  case Distracted = 'distracted';
  case Qualified = 'qualified';
  case Called = 'called';
}
