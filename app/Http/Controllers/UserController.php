<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
        ]);

        $user->update($validated);

        return response()->json($user->fresh());
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }

    /**
     * Get the user's orders.
     */
    public function orders(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'address', 'payment'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Get the order details.
     */
    public function orderDetail(Order $order, Request $request)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json(
            $order->load(['items.product', 'address', 'payment'])
        );
    }
}
