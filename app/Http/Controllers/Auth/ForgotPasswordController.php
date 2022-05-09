<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
}
