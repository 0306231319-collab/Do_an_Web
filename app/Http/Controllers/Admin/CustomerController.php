<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', User::ROLE_CUSTOMER)
            ->withCount('orders')
            ->orderByDesc('loyalty_points')
            ->orderBy('id')
            ->paginate(15);

        $vndPerPoint = (int) SystemConfig::getValue('loyalty_vnd_per_point', 10000);

        return view('admin.customers.index', compact('customers', 'vndPerPoint'));
    }
}
