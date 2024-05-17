<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;
	/**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('DevilApp')->accessToken;
            $success['user'] = $user;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Not Authorized'], 401);
        }
    }

	/**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
                'last_name' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
                'email' => 'required|email|unique:users|regex:%^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$%ix',
                'mobile' => ['required', 'regex:/^(\+201|01|00201)[0-2,5]{1}[0-9]{8}$/'],
                'type' => 'required|in:child,parent',
                'password' => 'required|min:5',
                'c_password' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] = $user->createToken('DevilApp')->accessToken;
            $success['user'] = $user;

            return response()->json(['success' => $success], $this->successStatus);
        } catch (QueryException $exception) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

	/**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function userDetails()
    {
        $user = Auth::user();
        return response()->json($user, $this-> successStatus);
    }
}
