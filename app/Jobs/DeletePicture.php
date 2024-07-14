<?php

namespace App\Jobs;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeletePicture implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $publicId;
    protected string $picture;

    /**
     * Create a new job instance.
     *
     * @param string $publicId
     * @param string $picture
     */
    public function __construct(string $publicId)
    {
        $this->publicId = $publicId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->publicId) {
            Cloudinary::destroy($this->publicId);
        }
    }
}
