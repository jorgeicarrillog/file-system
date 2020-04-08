<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Folder;

class FolderDeleting
{
    use SerializesModels;

    private $folder;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Folder $folder)
    {
        $this->folder = $folder;
        $this->deleteAll();
        return false;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function deleteAll()
    {
        $this->folder->files()->delete();
        $this->folder->folders()->delete();
    }
}
