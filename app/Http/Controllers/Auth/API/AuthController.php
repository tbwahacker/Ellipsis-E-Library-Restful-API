<?php

namespace App\Http\Controllers\Auth\API;

use App\Http\Library\ApiHelpers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\API\AuthBaseController as AuthBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends AuthBaseController
{

    use ApiHelpers;

    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;
            $success['email'] =  $user->email;
            $success['role'] =  $user->role;
            $success['id'] =  $user->id;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    public function register(Request $request){

        $encryptedPass = Hash::make($request->password);

        $user = new User;

        try{
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = 2;
            $user->password = $encryptedPass;
            $user->save();
            return $this->login($request);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => ''.$e
            ]);
        }
    }

    public function updateUserInfo(Request $request){
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;

        $user->update();

        return response()->json([
            'success' => true,
            'message' => 'successfully updated'
        ]);

    }

    public function getUsers(Request $request){
        $user = User::All();

        return response()->json([
            'success' => true,
            'users' => $user
        ]);

    }

    public function deleteUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if ($this->isAdmin($user)){
            $user = User::find($request->id);     // Find the id of the user passed
            if (Auth::user()->role==1){
                $user->delete();         // Delete the specific user
                if (!empty($user)){
                    return response()->json([
                        'success' => true,
                        'message' => 'user deleted'
                    ]);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'User Not Found'
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Only Admin Access needed'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized Access'
        ]);
    }

    public function logout(Request $request){
        try{
            // delete the current token that was used for the request
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'logout success'
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => ''.$e
            ]);
        }
    }

}
