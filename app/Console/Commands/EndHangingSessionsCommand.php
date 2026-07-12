<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SesiDaring;
use App\Services\SesiDaringService;
use Carbon\Carbon;
use Log;

class EndHangingSessionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:end-hanging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically end virtual sessions that are still live 30 minutes after schedule end time.';

    /**
     * Execute the console command.
     *
     * @param SesiDaringService $service
     * @return int
     */
    public function handle(SesiDaringService $service)
    {
        $now = Carbon::now();
        $cutoff = $now->subMinutes(30);

        // Find live sessions where schedule's waktu_selesai is older than 30 minutes ago
        $hangingSessions = SesiDaring::where('status_sesi', 'live')
            ->whereHas('jadwal', function ($query) use ($cutoff) {
                $query->where('waktu_selesai', '<', $cutoff);
            })
            ->get();

        $count = 0;
        foreach ($hangingSessions as $sesi) {
            try {
                $service->endLiveSession($sesi->id);
                $count++;
                $this->info("Successfully ended hanging session ID: {$sesi->id}");
            } catch (\Exception $e) {
                $this->error("Failed to end session ID {$sesi->id}: " . $e->getMessage());
                Log::error("Failed to auto-end hanging session ID {$sesi->id}: " . $e->getMessage());
            }
        }

        $this->info("Process completed. Ended {$count} hanging sessions.");
        return 0;
    }
}
