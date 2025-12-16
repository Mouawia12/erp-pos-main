<?php

namespace App\Http\Controllers\Api;

use App\Models\User; 
use App\Http\Controllers\Controller;
use App\Services\SingleDeviceLoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;  
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth; 
use Validator;


class AuthController extends Controller
{
    private SingleDeviceLoginService $singleDeviceLoginService;

    public function __construct(SingleDeviceLoginService $singleDeviceLoginService)
    {
        $this->singleDeviceLoginService = $singleDeviceLoginService;
    }

    public function login(Request $request)
    {
        try {

            $rules = [
                "email" => "required",
                "password" => "required"

            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) { 
                return  response()->json([
                    'message' => 'Error validator'
                ]);
            }

            $credentials = $request->only(['email', 'password']);
            $guard = Auth::guard('admin-web');

            if (!$guard->attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials!'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = $guard->user();
            $token = $this->singleDeviceLoginService->issueExclusiveToken($user, 'api-token');
            $cookie = cookie('jwt', $token->plainTextToken, 60 * 24); // 1 day 

            return response()->json([
                'message' => $token->plainTextToken,
                'user'=> $user
            ])->withCookie($cookie);
            /*
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required',
            ]);
         
            $user = User::where('email', $request->email)->first();
         
            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
             return $user->createToken($request->device_name)->plainTextToken;
            */
        } catch (\Exception $ex) {
            return response()->json([
                'code' => $ex->getCode(),
                'message'=> $ex->getMessage()
            ]);
        }

    }

    public function user()
    { 
       return Auth::user()->currentAccessToken();
    }

    public function logout(Request $request)
    {
        try { 
                $user = Auth::user(); 
                $cookie = Cookie::forget('jwt'); 

                if ($user) {
                    optional($user->currentAccessToken())->delete();
                    $this->singleDeviceLoginService->releaseSessionClaim($user);
                }

                return response()->json([
                    'message' => 'Success loggedOut'
                ])->withCookie($cookie);
           

        }catch (\Exception $ex){
            return  response()->json([
                'message' => 'Error loggedOut'
            ]);
        }

    }

 
}
