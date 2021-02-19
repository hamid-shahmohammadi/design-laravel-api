<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // validate the request
        $request->validate([
            'image' => ['required', 'mimes:jpeg,gif,bmp,png', 'max:2048']
        ]);

        //get the image
        $image = $request->file('image');
        $image_path=$image->getPathname();
        // Business Card.png => timestamp_business_card.png
        $filename=time()."_".preg_replace('/\s+/','_',strtolower($image->getClientOriginalName()));

        // move to uploads disk
        $upload_img=$image->storeAs('uploads/original',$filename,'uploads');

        $design=auth()->user()->designs()->create([
            'image'=>$filename,
            'disk'=>config('site.upload_disk')
        ]);

        $this->dispatch(new UploadImage($design));
        return response()->json($design,200);
    }
}
