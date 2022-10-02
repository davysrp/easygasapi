<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response(['message' => 'Validation errors', 'errors' =>  $validator->errors(), 'status' => false], 422);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        /**Take note of this: Your user authentication access token is generated here **/
        $data['token'] =  $user->createToken('MyApp')->accessToken;
        $data['name'] =  $user->name;

        return response(['data' => $data, 'message' => 'Account created successfully!', 'status' => true]);
    }


    public function loginWithPhone(Request $request)
    {
        $this->validate($request, [
            'phone_number' => 'required|regex:/[0-9]{10}/|digits:10',
        ]);

        $user = User::where('phone_number', $request->get('phone_number'))->first();

        $credentials = $request->only('phone_number');
        if(Auth::guard('user')->attempt($credentials)){
            $user = Auth::guard('user');
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['success' => $success]);
        }
        else{
            return response()->json(['error'=>'Email or password incorrect'], 401);
        }

    }


}
