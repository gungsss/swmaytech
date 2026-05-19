<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            $role = session()->get('role');
            return redirect()->to($role === 'admin' ? '/admin/dashboard' : '/user/kategori');
        }
        return view('welcome_message');
    }
}
