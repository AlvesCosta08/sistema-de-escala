<?php

namespace App\Notifications;

use App\Models\AvisoAusencia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AvisoAusenciaCriado extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AvisoAusencia $aviso)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'aviso_id' => $this->aviso->id,
            'titulo' => "Nova ausência: {$this->aviso->user->name}",
            'mensagem' => "{$this->aviso->motivoRotulo()} — {$this->aviso->alocacao->posto->nome}",
            'urgente' => $this->aviso->urgente,
            'url' => route('admin.ausencias.show', $this->aviso),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $msg = (new MailMessage)
            ->subject('Obede — Nova ausência registrada')
            ->line("{$this->aviso->user->name} marcou ausência.")
            ->line("Posto: {$this->aviso->alocacao->posto->nome}")
            ->line("Data: {$this->aviso->alocacao->data->isoFormat('ddd, DD/MM')}")
            ->line("Motivo: {$this->aviso->motivoRotulo()}");

        if ($this->aviso->urgente) {
            $msg->line('⚠ Aviso com menos de 24h de antecedência.');
        }

        return $msg->action('Ver no painel', route('admin.ausencias.show', $this->aviso));
    }
}