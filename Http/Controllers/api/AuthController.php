<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Models\Friends;
use App\Models\Posts;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {

        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userid=auth('api')->user()->id;

        $user =User::where('id',$userid)->get();
        $countposts=Posts::where('userid',$userid)->get();
        $numOfPosts=0;
        foreach($countposts as $countp){
            $numOfPosts++;
        }
        $frienduser=Friends::where('userFriendid',$userid)
        ->where('requestStatus','friends')
        ->get();
        $userfriend=Friends::where('userid',$userid)
        ->where('requestStatus','friends')
        ->get();
        $numOfFriends=0;
        foreach($frienduser as $friend){
            $numOfFriends++;
        }
        foreach($userfriend as $friend){
            $numOfFriends++;
        }

        $userdata=[];
        foreach($user as $use){
            $UD=array(
                "id"=>$use->id,
                "firstname"=>$use->firstname,
                "lastname"=>$use->lastname,
                "email"=>$use->email,
                "active_status"=>$use->active_status,
                "dark_mode"=>$use->dark_mode,
                "messenger_color"=>$use->messenger_color,
                "avatar"=>$use->avatar,
                "gender"=>$use->gender,
                "region"=>$use->region,
                "birthyear"=>$use->birthyear,
                "birthday"=>$use->birthday,
                "birthmonth"=>$use->birthmonth,
                "phonenumber"=>$use->phonenumber,
                "category"=>$use->category,
                "profileStatus"=>$use->profilestatus,
                "numOfPosts"=>$numOfPosts,
                "numOfFriends"=>$numOfFriends,
                "ban"=>$use->ban,
                "banReason"=>$use->banReason,
                "banDurationByDays"=>$use->banDurationByDays,
                "created_at"=>$use->created_at,
                "updated_at"=>$use->updated_at
                );
            array_push($userdata,$UD);
        }
        return response()->json(["token_data"=>$token,"User_info"=>$userdata]);

        // return $this->respondWithToken(["token_data"=>$token,"User_info"=>$userdata]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
