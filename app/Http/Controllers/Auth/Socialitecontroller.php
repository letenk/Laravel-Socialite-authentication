<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\socialAccount;
// 1. panggil package socialite
use Laravel\Socialite\Facades\Socialite;


class Socialitecontroller extends Controller
{
    // 2. buat method untuk meridirect ke sosial media yang akan di gunakan
    public function redirectToProvider($provider)
    {
        // return dari variable $provider untuk diarahkan ke soial media
        return Socialite::driver($provider)->redirect();
    }
    // 3. pengecekan fungsi apakah berhasil login atau tidak dan membeawa credentian/data dari aplikasi yang di tuju
    public function handleProviderCallback($provider)
    {
        // pengecekan apan data user ada 
        try {
            $user = Socialite::driver($provider)->user();
        } catch (\Throwable $th) {
            return redirect('/login');
        }

        // proses pengecekan jika user belum ada didata user lakukkan create user
            $authUser = $this->findOrCreate($user, $provider);
        // lakukkan proses login
            Auth::login($authUser, true);
        // jika berhasil arahkan ke home
            return redirect('/home');
    }

    // fungsi untuk membuat user
    // variable $socialUser adalah variable yang mendapatkan data dari variable $user di atas
    // dimana variable $user membawa data yang diadapt dari social media
    // var $provider adalah nama dari sosial media (facebook, twitter, dll)
    public function findOrCreateUser($socialUser, $provider)
    {
        // mencari provider id
        $socialAccount = SocialAccount::where($provider_id, $socialUser->getId())
        ->where('provider_name', $provider)
        ->first();

        // jika ada dapata dari social accaount di atas
        if($socialAccount){
            // maka retutn data dari sosial account tersebut
            return $socialAccount->user;
        // jika data belum ada
        }else{
            // cari data user di model User
            $user = User::where('email', $socialUser->getEmail())->first();

            //  dan jika data user tersebut tidak ada 
            if(! $user){
                // maka buat dulu datanya
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail()
                ]);
            }

            // dan jika data user sudah berhasil kita ambil , langsung create data usernya
            $user->socialAccounts()->create([
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider
            ]);

            return $user;
        }
    }
}
