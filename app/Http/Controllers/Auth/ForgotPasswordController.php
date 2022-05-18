<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\ResetUserPassword;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use \Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @throws NotFoundException
     */
    public function sendResetLinkEmail(Request $request)
    {
        // config(['app.locale' => $request->locale]);
        $request->validate(['email' => 'required|email']);

        $user = User::whereEmail($request->email)->first();

        if (!$user) {
            throw new NotFoundException(trans('auth.user_not_found'));
        }

        $status = (new PasswordResetLinkController())->store($request);

//        if( $status instanceof SuccessfulPasswordResetLinkRequestResponse){
//          $ok = true;
//        }

        return response()->json(['message' => trans('auth.email_syccessfully')]);
    }

    public function redirectToApp(Request $request, $token)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'redirect' => 'string|required|max:50'
        ]);

        if($validator->fails() || !$token){
            throw new \Exception('Error', 404);
        }

        return Redirect::to(config('app.frontend_url') . "/reset-verify?token=$token&".http_build_query($validator->validate()));
    }

    public function confirmPassword(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'redirect' => 'string|required|max:50',
            'password' => 'string|required|max:50',
            'token' => 'string|required',
            'password_confirmation' => 'string|required|max:50',
        ]);

        if($validator->fails()){
//            throw new \Exception('Error', 404);
        }

       $response = app(NewPasswordController::class)->store($request);

        if(true){
            throw new \Exception('erro');
        }


        $break = true;

//        new ResetUserPassword()

        throw new \Exception('Error11312132', 404);
        $status = (new ConfirmablePasswordController())->store($request);

        $aqui = true;
    }
}
