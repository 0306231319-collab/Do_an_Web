<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect($this->homeFor(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homeFor($request->user()));
    }

    public function showRegister()
{
    if (Auth::check()) {
        return redirect('/');
    }

    return view('auth.register');
}

public function register(Request $request)
{
    $data = $request->validate([
        'full_name' => ['required', 'string', 'max:100'],
        'email'     => ['required', 'email', 'unique:users,email'],
        'phone'     => ['required', 'regex:/^0\d{9}$/'],
        'password'  => ['required', 'min:6', 'confirmed'],
    ], [
        'full_name.required' => 'Vui lòng nhập họ tên.',
        'email.unique'       => 'Email này đã được đăng ký.',
        'phone.regex'        => 'Số điện thoại gồm 10 số, bắt đầu bằng 0.',
        'password.min'       => 'Mật khẩu tối thiểu 6 ký tự.',
        'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
    ]);

    $user = User::create([
        'full_name' => $data['full_name'],
        'email'     => $data['email'],
        'phone'     => $data['phone'],
        'password'  => \Illuminate\Support\Facades\Hash::make($data['password']),
        'role'      => User::ROLE_CUSTOMER,
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->intended('/');
}
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** Trang đích sau khi đăng nhập, theo vai trò */
    private function homeFor(User $user): string
    {
        return match ($user->role) {
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_FARM_STAFF => route('batches.index'),
            User::ROLE_SALES => route('sales.orders'),
            default => url('/'),
        };
    }
}
