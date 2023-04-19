<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::viaRequest('jwt', function (Request $request) {
            try {
                if (!$request->bearerToken()) {
                    return null;
                }
                $tokenPayload = JWT::decode($request->bearerToken(), new Key(config('jwt.key'), config('jwt.algo')));
            } catch(\Exception $e){
                Log::error($e);
                return null;
            }

            if ($this->isTokenRevoked($tokenPayload->jti)) {
                return null;
            }
            return User::findOrFail($tokenPayload->sub);
        });
    }

    private function isTokenRevoked($jti): bool
    {
        $key = 'jwt:invalidated:' . $jti;
        return Cache::has($key);
    }
}
