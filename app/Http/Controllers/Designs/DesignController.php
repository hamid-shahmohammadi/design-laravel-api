<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\IDesign;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use phpDocumentor\Reflection\Types\Collection;

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
            'tags'=>['required'],
            'team'=>['required_if:assign_to_team,true']
        ]);
        $design=$this->designs->update($design->id,[
            'team_id'=>$request->team,
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

    public function like($id){
        $this->designs->like($id);
        return response()->json(["message"=>"successful"],200);
    }

    public function checkIfHasLiked($designId){
        $isLiked=$this->designs->isLikedByUser($designId);
        return response()->json(["liked"=>$isLiked],200);
    }

    public function search(Request $request)
    {
        $designs=$this->designs->search($request);
        return DesignResource::collection($designs);
    }

    public function findBySlug($slug)
    {
        $design=$this->designs->withCriteria([
            new IsLive(),
            new EagerLoad(['user','comments'])
        ])->findWhereFirst('slug',$slug);
        return new DesignResource($design);
    }

    public function getForTeam($teamId)
    {
        $designs=$this->designs->withCriteria([new IsLive()])->findWhere('team_id',$teamId);
        return DesignResource::collection($designs);
    }

    public function getForUser($userId)
    {
        $designs=$this->designs
//            ->withCriteria([new IsLive()])
            ->findWhere('user_id',$userId);
        return DesignResource::collection($designs);
    }





}
