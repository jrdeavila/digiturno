<?php

namespace App\Traits;

trait ShiftUtils
{
    public function getStateLabel(?string $state = null): string
    {
        $map = [
            'pending' => 'Pendiente',
            'in_progress' => 'En progreso',
            'completed' => 'Completado',
            'called' => 'Llamada',
            'finished' => 'Finalizado',
            'cancelled' => 'Cancelado',
            'transferred' => 'Transferido',
            'pending-transferred' => 'Transferido pendiente',
            'qualified' => 'Calificado',
            'distracted' => 'Distraido',
        ];

        return $map[$this->state ?? $state] ?? 'Desconocido';
    }

    public function getStateColor(?string $state = null): string
    {
        $map = [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'called' => 'success',
            'finished' => 'success',
            'cancelled' => 'danger',
            'transferred' => 'warning',
            'pending-transferred' => 'warning',
            'qualified' => 'primary',
            'distracted' => 'danger',
        ];

        return $map[$this->state ?? $state] ?? 'secondary';
    }

    public function getStateIcon(?string $state = null): string
    {
        $map = [
            'pending' => 'fas fa-hourglass-half',
            'in_progress' => 'fas fa-clock',
            'completed' => 'fas fa-check',
            'called' => 'fas fa-phone',
            'finished' => 'fas fa-hourglass-end',
            'cancelled' => 'fas fa-ban',
            'transferred' => 'fas fa-exchange-alt',
            'pending-transferred' => 'fas fa-exchange-alt',
            'qualified' => 'fas fa-check',
            'distracted' => 'fas fa-check',
        ];

        return $map[$this->state ?? $state] ?? 'fas fa-hourglass-half';
    }
}
