<?php

namespace App\Jobs;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Image;
use File;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design=$design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk=$this->design->disk;
        $filename=$this->design->image;
        $original_file=public_path().'/uploads/original/'.$filename;

        try{
            Image::make($original_file)
                ->fit(800,600,function ($constraint){
                    $constraint->aspectRatio();
                })
                ->save($large=public_path('uploads/large/'.$filename));

            Image::make($original_file)
                ->fit(250,200,function ($constraint){
                    $constraint->aspectRatio();
                })
                ->save($large=public_path('uploads/thumbnail/'.$filename));

            $this->design->update([
                'upload_success'=>true
            ]);

        }catch (\Exeption $e){
            \Log::error($e->getMessage());
        }
    }
}
