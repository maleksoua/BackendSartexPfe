<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetRequest;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';


    /**
     * Reset the given user's password.
     *
     * @param PasswordResetRequest $request
     *
     * @return JsonResponse
     */
    public function reset(PasswordResetRequest $request)
    {
        $response = $this->broker('users')->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        return $response == Password::PASSWORD_RESET ?
            $this->sendResetResponse($request, $response) :
            $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param PasswordResetRequest $request
     * @param                      $response
     *
     * @return JsonResponse
     */
    protected function sendResetResponse(PasswordResetRequest $request, $response)
    {
        return response()->json([
            'status' => 'success',
            'message' => __($response)
        ], 200);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param PasswordResetRequest $request
     * @param                      $response
     *
     * @return JsonResponse
     */
    protected function sendResetFailedResponse(PasswordResetRequest $request, $response)
    {
        return response()->json([
            'status' => 'error',
            'message' => __($response)
        ], 401);
    }

    /**
     * Get the password broker
     *
     * @param string $name
     *
     * @return PasswordBroker
     */
    public function broker($name)
    {
        return Password::broker($name);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ];
    }
}
