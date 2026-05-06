<?php
 
namespace App\Jobs;
 
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
 
class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 
    public int $tries = 3;           // يحاول 3 مرات لو فشل
    public int $backoff = 60;        // ينتظر 60 ثانية بين كل محاولة
 
    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $phone,
        public string $message
    ) {}
 
    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsapp): void
    {
        $whatsapp->sendMessage($this->phone, $this->message);
    }
}
