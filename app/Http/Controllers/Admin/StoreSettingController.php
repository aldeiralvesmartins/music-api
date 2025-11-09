<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\StoreSettingStoreRequest;
use App\Http\Requests\Admin\StoreSettingUpdateRequest;

class StoreSettingController extends Controller
{
    // Public endpoint: returns the latest active settings
    public function publicShow()
    {
        // Latest active settings belonging to any admin user
        $settings = StoreSetting::where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('is_admin', true)->orWhere('type', 'Admin');
            })
            ->latest('created_at')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    // Admin: list all settings
    public function index()
    {
        $data = StoreSetting::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // Admin: create settings
    public function store(StoreSettingStoreRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $settings = StoreSetting::create($data);

        return response()->json(['success' => true, 'data' => $settings], 201);
    }

    // Admin: show one
    public function show(StoreSetting $storeSetting)
    {
        $this->authorizeOwnership($storeSetting);
        return response()->json(['success' => true, 'data' => $storeSetting]);
    }

    // Admin: update
    public function update(StoreSettingUpdateRequest $request, StoreSetting $storeSetting)
    {
        $this->authorizeOwnership($storeSetting);
        $data = $request->validated();
        $storeSetting->update($data);
        return response()->json(['success' => true, 'data' => $storeSetting]);
    }

    // Admin: delete
    public function destroy(StoreSetting $storeSetting)
    {
        $this->authorizeOwnership($storeSetting);
        $storeSetting->delete();
        return response()->json(['success' => true]);
    }

    // Admin: toggle or set is_active
    public function toggle(Request $request, StoreSetting $storeSetting)
    {
        $this->authorizeOwnership($storeSetting);
        $data = $request->validate([
            'is_active' => ['nullable', 'boolean'],
        ]);

        $newValue = array_key_exists('is_active', $data)
            ? (bool) $data['is_active']
            : !$storeSetting->is_active;

        DB::transaction(function () use ($storeSetting, $newValue) {
            if ($newValue) {
                // Deactivate all other settings of this admin
                StoreSetting::where('user_id', $storeSetting->user_id)
                    ->where('id', '!=', $storeSetting->id)
                    ->update(['is_active' => false]);
            }

            $storeSetting->is_active = $newValue;
            $storeSetting->save();
        });

        return response()->json(['success' => true, 'data' => $storeSetting->fresh()]);
    }

    // Validation now handled by Form Requests

    private function authorizeOwnership(StoreSetting $storeSetting): void
    {
        abort_if($storeSetting->user_id !== Auth::id(), 403, 'Forbidden');
    }
}
