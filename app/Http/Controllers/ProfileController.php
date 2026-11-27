<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)
            ->orderByDesc('ngay_tao')
            ->take(5)
            ->get();

        $orderCount = Order::where('user_id', $user->id)->count();

        return view('pages.profile', compact('user', 'orders', 'orderCount'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone'     => ['required', 'regex:/^[0-9]{9,15}$/', Rule::unique('users', 'phone')->ignore($user->id)],
        ], [
            'full_name.required' => 'Vui lòng nhập họ tên.',
            'phone.required'     => 'Vui lòng nhập số điện thoại.',
            'phone.regex'        => 'Số điện thoại chỉ gồm 9 đến 15 chữ số.',
            'phone.unique'       => 'Số điện thoại này đã được sử dụng.',
        ]);

        $user->update($data);

        return redirect()->route('profile')->with('status', 'Đã cập nhật thông tin cá nhân.');
    }
}
