<?php

namespace App\Observers;

use App\Models\Cita;
use Filament\Notifications\Notification;

class CitaObserver
{
    public function created(Cita $cita): void
    {
        if ($cita->fuente === 'web') {
            Notification::make()
                ->title('🎉 Nueva cita desde la web')
                ->body("{$cita->paciente->nombre} solicitó cita para {$cita->fecha_hora->format('d/m/Y H:i')}")
                ->warning()
                ->send();
        }
    }
}