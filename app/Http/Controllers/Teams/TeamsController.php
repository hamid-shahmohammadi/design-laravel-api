<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamsController extends Controller
{
    protected $teams;
    protected $users;
    protected $invitations;

    public function __construct(ITeam $teams,IUser $users,IInvitation $invitations)
    {
        $this->teams=$teams;
        $this->users=$users;
        $this->invitations=$invitations;
    }
    public function index()
    {
        # code...
    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>['required','string','max:80','unique:teams,name']
        ]);

        $team=$this->teams->create([
            'owner_id'=>auth()->id(),
            'name'=>$request->name,
            'slug'=>Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }
    public function update(Request $request,$id)
    {
        $team=$this->teams->find($id);

        $this->authorize('update',$team);

        $this->validate($request,[
            'name'=>['required','string','max:80','unique:teams,name,'.$id]
        ]);

        $team=$this->teams->update($id,[
            'name'=>$request->name,
            'slug'=>Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }
    public function findById($id)
    {
        $team=$this->teams->find($id);
        return new TeamResource($team);
    }
    public function fetchUserTeams()
    {
        $teams=$this->teams->fetchUserTeams();
        return TeamResource::collection($teams);
    }
    public function findBySlug()
    {
        # code...
    }

    public function removeFromTeam($teamId,$userId)
    {
        $team=$this->teams->find($teamId);
        $user=$this->users->find($userId);

        if($user->isOwnerOfTeam($team)){
            return response()->json([
                'message'=>'You are the team owner'
            ],401);
        }
        if(!auth()->user()->isOwnerOfTeam($team) && auth()->id()!==$user->id){
            return response()->json([
                'message'=>'You cannot do this'
            ],401);
        }

        $this->invitations->removeUserFromTeam($team,$userId);
        return response()->json([
            'message'=>'Success'
        ],200);
    }
}
