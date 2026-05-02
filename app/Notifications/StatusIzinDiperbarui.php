<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\Perizinan\Models\Perizinan;

class StatusIzinDiperbarui extends Notification
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
        $isApproved = $this->perizinan->status === 'disetujui';
        
        return [
            'icon' => $isApproved ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger',
            'title' => 'Status Pengajuan ' . strtoupper($this->perizinan->status),
            'message' => 'Pengajuan izin ' . strtoupper($this->perizinan->jenis_izin) . ' Anda telah ' . strtolower($this->perizinan->status),
            'url' => route('perizinan.show', $this->perizinan->id),
            'time' => now()->diffForHumans(),
        ];
    }
}
