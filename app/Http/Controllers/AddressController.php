<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the addresses.
     */
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->latest()->get();
        return response()->json($addresses);
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zip_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
//            'country' => 'required|string|max:100',
//            'reference' => 'nullable|string|max:255',
//            'is_default' => 'boolean',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            if ($validated['is_default'] ?? false) {
                $request->user()->addresses()->update(['is_default' => false]);
            } elseif (!$request->user()->addresses()->exists()) {
                $validated['is_default'] = true;
            }

            $address = $request->user()->addresses()->create($validated);

            return response()->json($address, 201);
        });
    }

    /**
     * Display the specified address.
     */
    public function show(Address $address)
    {
        $this->authorize('view', $address);
        return response()->json($address);
    }

    /**
     * Update the specified address in storage.
     */
    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'zip_code' => 'sometimes|required|string|max:10',
            'street' => 'sometimes|required|string|max:255',
            'number' => 'sometimes|required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|size:2',
            'country' => 'sometimes|required|string|max:100',
            'reference' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        return DB::transaction(function () use ($request, $address, $validated) {
            if ($validated['is_default'] ?? false) {
                $request->user()->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);

            return response()->json($address);
        });
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        // Prevent deleting the default address if it's the only one
        if ($address->is_default && $address->user->addresses()->count() === 1) {
            return response()->json([
                'message' => 'Cannot delete the only address. Please add another address first.'
            ], 422);
        }

        $address->delete();

        return response()->json(null, 204);
    }

    /**
     * Set an address as default.
     */
    public function setAsDefault(Address $address)
    {
        $this->authorize('update', $address);

        DB::transaction(function () use ($address) {
            $address->user->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);

            $address->update(['is_default' => true]);
        });

        return response()->json($address);
    }
}
