<?php

namespace App\Core\Authorization\Http\Controllers;

use App\Core\Authorization\Services\AuthorizationService;
use App\Core\Http\Controllers\BaseApiController;
use App\Core\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRightController extends BaseApiController
{
    public function __construct(
        private AuthorizationService $authorizationService
    )
    {
    }

    public function assign(Request $request, string $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $data = $request->validate([
            'right_id' => 'required|exists:rights,id',
        ]);

        $this->authorizationService->assignRightToUser($user, $data['right_id']);
        return $this->success($user->fresh('rights'), 'Right assigned successfully');
    }

    public function remove(Request $request, string $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $data = $request->validate([
            'right_id' => 'required|exists:rights,id',
        ]);

        $this->authorizationService->removeRightFromUser($user, $data['right_id']);
        return $this->success($user->fresh('rights'), 'Right removed successfully');
    }

    public function sync(Request $request, string $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $data = $request->validate([
            'right_ids' => 'required|array',
            'right_ids.*' => 'exists:rights,id',
        ]);

        $this->authorizationService->syncUserRights($user, $data['right_ids']);
        return $this->success($user->fresh('rights'), 'Rights synced successfully');
    }
}
