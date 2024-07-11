<?php

namespace App\Enums;

enum ShiftModuleAssignationState: string
{
  case Completed = 'completed';
  case Assigned = 'assigned';
  case Transferred = 'transferred';
}
