<?php

namespace App\Enums;

enum ShiftSpanishLabel: string
{
  case Pending = 'Pendiente';
  case PendingTransferred = 'Pendiente Transferido';
  case Transferred = 'Transferido';
  case InProgress = 'En Progreso';
  case Completed = 'Completado';
  case Cancelled = 'Cancelado';
  case Distracted = 'Distraido';
  case Qualified = 'Calificado';
  case Called = 'Llamado';
}
