<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\NewAccessToken;

class SingleDeviceLoginService
{
    public function isEnabledFor(?User $user): bool
    {
        return (bool) $user;
    }

    public function ensureExclusiveWebSession(Request $request, User $user, ?string $password = null): void
    {
        if (!$this->isEnabledFor($user)) {
            return;
        }

        $currentSessionId = $request->session()->getId();

        if ($password) {
            Auth::guard('admin-web')->logoutOtherDevices($password);
        }

        $this->invalidateStoredSession($user->session_id, $currentSessionId);
        $this->revokeTokens($user);

        $user->forceFill(['session_id' => $currentSessionId])->save();
    }

    public function issueExclusiveToken(User $user, string $tokenName = 'token'): NewAccessToken
    {
        $previousSessionId = $user->session_id;

        if ($this->isEnabledFor($user)) {
            $this->revokeTokens($user);
            $this->invalidateStoredSession($previousSessionId);
        }

        $token = $user->createToken($tokenName);

        if ($this->isEnabledFor($user)) {
            $user->forceFill([
                'session_id' => 'token-' . $token->accessToken->id,
            ])->save();
        }

        return $token;
    }

    public function releaseSessionClaim(User $user): void
    {
        if (!$this->isEnabledFor($user)) {
            return;
        }

        $user->forceFill(['session_id' => null])->save();
    }

    public function invalidateStoredSession(?string $sessionId, ?string $exceptSessionId = null): void
    {
        if (!$sessionId || $sessionId === $exceptSessionId) {
            return;
        }

        try {
            $handler = Session::getHandler();
            if (method_exists($handler, 'destroy')) {
                $handler->destroy($sessionId);
            }
        } catch (\Throwable $e) {
            // Best-effort: failure here should not block the request flow.
        }
    }

    private function revokeTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
