<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\Contracts\IUser;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

//    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
//    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $users;
    public function __construct(IUser $users)
    {
//        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->users=$users;
    }

    public function verify(Request $request,$id)
    {
        $user=User::find($id);
        if(! URL::hasValidSignature($request)){
            return response()->json(["errors"=>[
                "message"=>"invalid verification link"
            ]],422);
        }
        if($user->hasVerifiedEmail()){
            return response()->json(["errors"=>[
                "message"=>"Email address already verified"
            ]],422);
        }
        $user->markEmailAsVerified();
        event(new Verified($user));
        return response()->json(['message'=>'Email successfully verified'],200);
    }

    public function resend(Request $request)
    {
        $this->validate($request,[
            'email'=>['email','required']
        ]);
        $user=$this->users->findWhereFirst('email',$request->email);
        if(!$user){
            return response()->json(['errors'=>[
                'message'=>'No user could be found with this email'
            ]],422);
        }
        if($user->hasVerifiedEmail()){
            return response()->json(['errors'=>[
                'message'=>'Email address already verified'
            ]],422);
        }
        $user->sendEmailVerificationNotification();
        return response()->json(['status'=>'verification link resend']);
    }
}
