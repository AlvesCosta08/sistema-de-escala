<?php

namespace App\Notifications;

use App\Models\AvisoAusencia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubstitutoAlocado extends Notification implements ShouldQueue
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
        $souSubstituto = $notifiable->id === $this->aviso->substituto_user_id;

        return [
            'aviso_id' => $this->aviso->id,
            'titulo' => $souSubstituto
                ? "Você foi alocado no lugar de {$this->aviso->user->name}"
                : "{$this->aviso->substituto->name} cobrirá sua ausência",
            'mensagem' => "{$this->aviso->alocacao->posto->nome} — {$this->aviso->alocacao->data->isoFormat('ddd, DD/MM')}",
            'url' => route('minha-escala'),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $souSubstituto = $notifiable->id === $this->aviso->substituto_user_id;

        $msg = (new MailMessage)
            ->subject('Obede — Substituição alocada')
            ->line("Posto: {$this->aviso->alocacao->posto->nome}")
            ->line("Data: {$this->aviso->alocacao->data->isoFormat('ddd, DD/MM')}")
            ->line("Turno: {$this->aviso->alocacao->turno->nome}");

        if ($souSubstituto) {
            $msg->line("Você foi alocado no lugar de {$this->aviso->user->name}.");
        } else {
            $msg->line("{$this->aviso->substituto->name} cobrirá sua ausência.");
        }

        return $msg->action('Ver minha escala', route('minha-escala'));
    }
}