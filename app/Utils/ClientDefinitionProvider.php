<?php

namespace App\Utils;

abstract class ClientDefinitionProvider
{
    public static function getDefinition($clientType): mixed
    {
        return ([
            'standard' =>  [
                'icon' => 'fas fa-user',
                'color' => 'bg-primary',
                'text' => 'Estándar'
            ],
            'preferential' => [
                'icon' => 'fas fa-star',
                'color' => 'bg-info',
                'text' => 'Preferencial'
            ],
            'processor' => [
                'icon' => 'fas fa-bolt',
                'color' => 'bg-warning',
                'text' => 'Tramitador'
            ],
        ])[$clientType];
    }
}
