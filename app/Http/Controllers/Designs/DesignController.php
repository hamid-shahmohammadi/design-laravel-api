<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    protected $designs;
    public function __construct(IDesign $design)
    {
        $this->designs=$design;
    }

    public function findDesign($id)
    {
        $design=$this->designs->find($id);
        return new DesignResource($design);
    }
    public function index()
    {
        $designs=$this->designs->withCriteria(
            new LatestFirst(),
            new IsLive(),
            new ForUser(1),
            new EagerLoad(['user','comments'])
        )->all();
        return DesignResource::collection($designs);
    }
    public function update(Request $request,Design $design)
    {
        $this->authorize('update',$design);
        $this->validate($request,[
           'title'=>['required','unique:designs,title,'.$design->id],
            'description'=>['required','string','min:20','max:140'],
            'tags'=>['required']
        ]);
        $design=$this->designs->update($design->id,[
            'title'=>$request->title,
            'description'=>$request->description,
            'slug'=>Str::slug($request->title),
            'is_live'=> !$design->upload_success ? false : $request->is_live,
        ]);
        $this->designs->applyTags($design->id,$request->tags);

        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design=$this->designs->find($id);
        $this->authorize('delete',$design);
        foreach (['thumbnail','large','original'] as $size){
            if(Storage::disk($design->disk)->exists("uploads/{$size}/".$design->image)){
                Storage::disk($design->disk)->delete("uploads/{$size}/".$design->image);
            }
        }
        $this->designs->delete($id);
        return response()->json(["message"=>"Record deleted"],200);
    }
}
