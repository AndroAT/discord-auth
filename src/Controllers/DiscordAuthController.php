<?php

namespace Azuriom\Plugin\DiscordAuth\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DiscordAuth\Models\Discord;
use Azuriom\Models\User;
use Azuriom\Rules\Username;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class DiscordAuthController extends Controller
{
    /**
     * Redirect the user to the Discord authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('discord')
            ->scopes(['identify', 'email', 'guilds'])
            ->redirect();
    }

    /**
     * Obtain the user information from Discord.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        try {
            $user = Socialite::driver('discord')->user();

            $discordId = $user->user['id'];
            $email = $user->user['email'];
            $created = false;

            $discord = Discord::with('user')->where('discord_id', $discordId)->first();

            if (!$discord) {
                if (Auth::guest() && User::where('email', $email)->exists()) {
                    return redirect()->route('login')
                        ->with('error', trans('discord-auth::messages.email_already_exists'));
                }

                $userToLogin = Auth::user() ?? User::forceCreate([
                    'name' => $discordId,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)),
                    'last_login_ip' => $request->ip(),
                    'last_login_at' => now(),
                ]);

                $discord = new Discord();
                $discord->discord_id = $discordId;
                $discord->user_id = $userToLogin->id;
                $discord->username = $user->user['username'];
                $discord->discriminator = $user->user['discriminator'];
                $discord->avatar = $user->user['avatar'];
                $discord->save();

                $created = true;
            }

            if ($userToLogin->isBanned()) {
                throw ValidationException::withMessages([
                    'email' => trans('auth.suspended'),
                ])->redirectTo(URL::route('login'));
            }

            Auth::login($userToLogin, true);

            if ($created) {
                return redirect()->route('discord-auth.username');
            }

            return redirect()->route('home');
        } catch (\Exception $e) {
            \Log::error('Discord auth error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function username()
    {
        return view('discord-auth::username', ['conditions' => setting('discord-auth.conditions')]);
    }

    public function registerUsername(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:25', 'unique:users', new Username()]
        ]);

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();

        return redirect()->route('home');
    }
}
