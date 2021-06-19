<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;
use App\Rules\CheckSamePassword;
use App\Rules\MatchOldPassword;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    protected $users;
    public function __construct(IUser $users)
    {
        $this->users=$users;
    }
    public function updateProfile(Request $request)
    {


        $user=auth()->user();
        $this->validate($request,[
           'tagline' => ['required'],
           'name' => ['required'],
           'about' => ['required'],
           'formatted_address' => ['required'],
           'latitude' => ['required','min:-90','max:90'],
           'longitude' => ['required','min:-180','max:180'],
        ]);
        $location=new Point($request->latitude,$request->longitude);
        $user=$this->users->update(auth()->id(),[
            'name'=>$request->name,
            'formatted_address'=>$request->formatted_address,
            'location'=>$location,
            'available_to_hire'=>$request->available_to_hire,
            'about'=>$request->about,
            'tagline'=>$request->tagline,
        ]);
        return new UserResource($user);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request,[
            'current_password'=>['required',new MatchOldPassword],
            'password'=>['required',new CheckSamePassword]
        ]);
        $request->user()->update([
            'password'=>bcrypt($request->password)
        ]);
        return response()->json(['message'=>'password updated'],200);
    }
}
