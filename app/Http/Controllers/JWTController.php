<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'showRegisterForm']]);
    }

    public function showRegisterForm()
    {
        $majors = Major::pluck('name', 'id')->toArray();
        return response()->json(['next_url' => \route('api.auth.verification-code'), 'nex_method' => 'post', 'expected_inputs' => 'number,major,code,invitation_code', 'majors' => $majors], 200);
    }

    /**
     * Register user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        if($request->has('firebase_token')){
            $user->firebase_token = $request->firebase_token;
            $user->save();
        }
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function flogin(Request $request){
        $message = [
            'to'=> "dM5UuZYjTkebui7-Q8KyTF:APA91bFpOr_Hv9zf4FDwO35ZGj1ywG05yIInS1KTRi6VJqmZdCHF_Fkt5NGUC-pffVinbgUKfmaq_tFitYsFpnV-KAcCc2q-nBufDCBsQKovEWSyKW3W8EhFvV12i89u-3IXXcbIoEFK",
            'notification'=>[
                "title"=> "Статус обновлен4 !!!",
                "body"=> "Статус обновлен На 'Одобрено3'"
            ]
        ];


        return $message;
    }
    /**
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
//        return $request->toArray();
        $fieldType = isset($request['email']) ? 'email' : 'number';

        $validator = Validator::make($request->all(), [
            $fieldType => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }
        JWTAuth::factory()->setTTL(1440000);
        if (!$token = auth()->guard('api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where((($fieldType=='email') ? 'email' : 'number'), $request[$fieldType] )->first();
        if($request->has('firebase_token')){
            $user->firebase_token = $request->firebase_token;
            $user->save();
        }

        return $this->respondWithToken($token);
    }



    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully logged out.']);
    }

    /**
     * Refresh token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get user profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $info=[];
        $info['numberOfNewNotifications']=\ViewService::init()->view('numberOfNewNotifications');
        $info['headerNotifications']=\ViewService::init()->view('headerNotifications');
        $info['user_id']=auth()->user()->id;
        $info['qr_code']=asset( 'qrcodes/qrqr_'.auth()->user()->id.'.png');
        $info['paymentAmountsDetailInfo']=\ViewService::init()->view('paymentAmountsDetail');
        $info['balance']=auth()->user()->balance->balance;
        $info['freezed']=auth()->user()->SumOfPendingAmount();
        $info['paid']=auth()->user()->sum_of_paids();
        $info['firebase_token']=auth()->user()->firebase_token;



        return response()->json($info);
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
        ]);
    }
    public function guard()
    {
        return Auth::guard();
    }
}
