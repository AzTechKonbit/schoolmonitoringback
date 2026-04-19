<?php

namespace App\Core\Authorization\Http\Controllers;

use App\Core\Authorization\Models\Title;
use App\Core\Authorization\Services\AuthorizationService;
use App\Core\Http\Controllers\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TitleController extends BaseApiController
{
    public function __construct(
        private AuthorizationService $authorizationService
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search']);
        $titles = $this->authorizationService->getAllTitles($filters, $request->get('per_page', 15));
        return $this->paginate($titles);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => 'required|string|max:45|unique:titles,code',
            'title' => 'required|string|max:45',
            'description' => 'nullable|string|max:45',
        ]);

        $title = $this->authorizationService->createTitle($data);
        return $this->success($title, 'Title created successfully', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $title = Title::find($id);

        if (!$title) {
            return $this->error('Title not found', 404);
        }

        $data = $request->validate([
            'code' => 'sometimes|string|max:45|unique:titles,code,' . $id,
            'title' => 'sometimes|string|max:45',
            'description' => 'nullable|string|max:45',
        ]);

        $title = $this->authorizationService->updateTitle($title, $data);
        return $this->success($title, 'Title updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $title = Title::find($id);

        if (!$title) {
            return $this->error('Title not found', 404);
        }

        $this->authorizationService->deleteTitle($title);
        return $this->success(null, 'Title deleted successfully');
    }
}
