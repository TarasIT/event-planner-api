<?php

namespace App\Jobs;

use App\Models\User;
use Cloudinary\Api\Admin\AdminApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteAllPictures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    /**
     * Create a new job instance.
     */
    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::findOrFail($this->user_id);
        $folderName = $user->name;
        $adminApi = new AdminApi();
        $adminApi->deleteAllAssets([
            'folder' => $folderName
        ]);
        $subFolders = $adminApi->subFolders("events");
        $isFolderExisted = false;

        foreach ($subFolders['folders'] as $folder) {
            if ($folder['name'] === $folderName) {
                $isFolderExisted = true;
                break;
            }
        }

        $pictures = $adminApi->assetsByAssetFolder("events/$folderName");
        $picturesCount = $pictures["total_count"];
        if ($isFolderExisted  && !$picturesCount) {
            $adminApi->deleteFolder("events/$folderName");
        }
    }
}
