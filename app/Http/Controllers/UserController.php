<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Http\Request;
use App\Helper\ResponseHelper;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function UserLogin(Request $request)
    {
        try {
            $UserEmail = $request->UserEmail;
            $OTP = rand(100000, 999999);
            $details = ['otp' => $OTP];
            Mail::to($UserEmail)->send(new OTPMail($details));
            User::updateOrCreate(['email' => $UserEmail], ['email' => $UserEmail, 'otp' => $OTP]);
            return ResponseHelper::Out('success', "A 6 Digit OTP has been send to your email address", 200);
        } catch (Exception $e) {
            return ResponseHelper::Out('fail', $e->getMessage(), 200);
        }
    }
  
    // VerifyLogin method in UserController
    public function VerifyLogin($UserEmail, $OTP)
    {
        $verification = User::where('email', $UserEmail)->where('otp', $OTP)->first();

        if ($verification) {

            User::where('email', $UserEmail)->where('otp', $OTP)->update(['otp' => '0']);

            // Determine the role based on 'is_admin' field
            $role = $verification->is_admin == 1 ? 'admin' : 'user';  // Assuming 'is_admin' defines the role

            // Create JWT token with user role
            $token = JWTToken::CreateToken($verification->email, $verification->id, $role);


            // Return the token in a cookie
             return ResponseHelper::Out('success', $token, 200)->cookie('token', $token, 60);
        } else {
            return ResponseHelper::Out('fail', null, 401);
        }
    }



    function UserLogout()
    {
        return redirect('/')->cookie('token', '', -1);
    }


}
