<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Validation\ValidationException;


class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        
        if ($user->two_factor_enabled) {
            $google2fa = app('pragmarx.google2fa');

            // Generate the QR code URL
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'), // Application name
                $user->email, // User email
                $user->google2fa_secret // User's 2FA secret
            );

            // Generate the actual QR code image
            $qrCodeImage = QrCode::size(200)->generate($qrCodeUrl);

            return view('auth.2fa', compact('qrCodeImage','user'));
        }
        return redirect()->route('home')->with('error', 'Two-factor authentication is not enabled.');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        // dd($request->all());

        $user_id = $request->session()->get('2fa:user:id');
        $remember = $request->session()->get('2fa:auth:remember', false);
        $attempt = $request->session()->get('2fa:auth:attempt', false);

        if (!$user_id || !$attempt) {
            return redirect()->route('login');
        }

        $user = User::find($user_id);

        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $otp_secret = $user->google2fa_secret;

        if (!$google2fa->verifyKey($otp_secret, $request->one_time_password)) {
            throw ValidationException::withMessages([
            'one_time_password' => [__('The one time password is invalid.')],
            ]);
        }

        $guard = config('auth.defaults.guard');
        $credentials = [$user->getAuthIdentifierName() => $user->getAuthIdentifier(), 'password' => $user->getAuthPassword()];
        
        if ($remember) {
            $guard = config('auth.defaults.remember_me_guard', $guard);
        }
        
        if ($attempt) {
            $guard = config('auth.defaults.attempt_guard', $guard);
        }
        
        if (Auth::guard($guard)->attempt($credentials, $remember)) {
            $request->session()->remove('2fa:user:id');
            $request->session()->remove('2fa:auth:remember');
            $request->session()->remove('2fa:auth:attempt');
        
            return redirect()->intended('/');
        }
        
        return redirect()->route('login')->withErrors([
            'password' => __('The provided credentials are incorrect.'),
        ]);
    }
}
