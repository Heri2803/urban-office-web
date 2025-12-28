<?php
// app/Console/Commands/UpdateRoomStatus.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoomStatusService;

class UpdateRoomStatus extends Command
{
    protected $signature = 'rooms:update-status';
    protected $description = 'Update room status based on active transactions';

    public function handle(RoomStatusService $roomStatusService)
    {
        $this->info('Updating room statuses...');
        $roomStatusService->updateAllRoomStatus();
        $this->info('Room statuses updated successfully!');
    }
}