<?php

namespace App\Console\Commands\DevTools\Telegram;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:tg:set-webhook {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Telegram webhook for a bot with self-signed SSL';

    /**
     * Execute the console command.
     * @throws ConnectionException
     */
    public function handle(): int
    {
        $token = $this->argument('token');

        $webhookUrl = config("app.url") . "telegraph/{$token}/webhook";
        $certificatePath = config("telegraph.ssl.self_signed");

        if (!file_exists($certificatePath)) {
            $this->error("Certificate not found at {$certificatePath}");
            return 1;
        }

        $this->info("Setting webhook for bot token: {$token}");
        $this->info("Webhook URL: {$webhookUrl}");

        $response = Http::attach(
            'certificate',
            file_get_contents($certificatePath),
            'public.pem'
        )->post("https://api.telegram.org/bot{$token}/setWebhook", [
            'url' => $webhookUrl,
        ]);

        if ($response->successful()) {
            $this->info("Webhook successfully set!");
        } else {
            $this->error("Failed to set webhook: " . $response->body());
        }

        return 0;
    }
}
