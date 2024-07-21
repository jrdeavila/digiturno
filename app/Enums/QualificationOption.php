<?php

namespace App\Enums;

enum QualificationOption: string
{
  case Bad = 'bad';
  case Regular = 'regular';
  case Good = 'good';
  case Excellent = 'excellent';
  case NotQualified = 'no_qualified';
}
