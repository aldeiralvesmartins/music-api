<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total'),
            'recent_orders' => Order::with(['user', 'items'])
                ->latest()
                ->take(5)
                ->get(),
            'top_products' => DB::table('order_items')
                ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_quantity', 'desc')
                ->take(5)
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * List all users with pagination
     */
    public function listUsers(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $users = User::withCount(['orders', 'addresses'])
            ->latest()
            ->paginate($perPage);

        return response()->json($users);
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'document' => 'sometimes|nullable|string|max:20',
            'is_admin' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Delete a user
     */
    public function deleteUser(string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * List all orders with filters
     */
    public function listOrders(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        
        $query = Order::with(['user', 'items.product'])
            ->when($status, function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->latest();

        return response()->json($query->paginate($perPage));
    }

    /**
     * Update order status
     */
    public function updateOrder(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        // Notify user about order status update
        // $order->user->notify(new OrderStatusUpdated($order));

        return response()->json($order);
    }
}
