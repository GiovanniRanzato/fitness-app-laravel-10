<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
 
        $status = Password::sendResetLink(
            $request->only('email')
        );     
        return $status === Password::RESET_LINK_SENT
                    ? response(['message' => 'Success'], 200)
                    : response(['message' => 'Error sending reset link email.'], 500);
    }

    public function reset(Request $request)
    {
       return redirect(env('FE_BASE_URL').'/reset-password/?token='.$request->token);
    }

    public function passwordUpdate(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);
        
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ]);
        
                    $user->save();
        
                    event(new PasswordReset($user));
                }
            );
            if ($status === Password::PASSWORD_RESET)
                return response(['message' => 'Success'], 200);
            
            throw new Exception($status);

        } catch(Exception $e){
            Log::error($e->getMessage());
            return response(['message' => 'Error updating password.'.$e->getMEssage()], 500);
        }
    }
}
