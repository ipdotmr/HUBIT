<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Microsoft 365 login
     */
    public function redirectToMicrosoft()
    {
        return Socialite::driver('azure')->redirect();
    }

    /**
     * Handle Microsoft 365 callback
     */
    public function handleMicrosoftCallback()
    {
        try {
            $azureUser = Socialite::driver('azure')->user();
            
            $user = User::where('email', $azureUser->getEmail())->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $azureUser->getName(),
                    'email' => $azureUser->getEmail(),
                    'password' => Hash::make(Str::random(32)), // Random password since they'll use SSO
                    'email_verified_at' => now(),
                    'azure_id' => $azureUser->getId(),
                ]);
            } else {
                if (!$user->azure_id) {
                    $user->update(['azure_id' => $azureUser->getId()]);
                }
            }
            
            Auth::login($user, true);
            
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Unable to login with Microsoft 365. Please try again or use email/password.',
            ]);
        }
    }
}
