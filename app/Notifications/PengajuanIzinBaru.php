<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Perizinan\Models\Perizinan;

class PengajuanIzinBaru extends Notification
{
    use Queueable;

    public $perizinan;

    /**
     * Create a new notification instance.
     */
    public function __construct(Perizinan $perizinan)
    {
        $this->perizinan = $perizinan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'fas fa-envelope-open-text text-primary',
            'title' => 'Pengajuan Izin Baru',
            'message' => 'Terdapat pengajuan izin baru dari ' . ($this->perizinan->pegawai->nama ?? 'Pegawai'),
            'url' => route('perizinan.show', $this->perizinan->id),
            'time' => now()->diffForHumans(),
        ];
    }
}
