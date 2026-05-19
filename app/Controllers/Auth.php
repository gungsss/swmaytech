<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to($this->getRedirectUrl());
        }
        return view('auth/login');
    }

    public function doLogin()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password harus diisi.');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->getUserByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        if ($user['status'] !== 'aktif') {
            return redirect()->back()->with('error', 'Akun Anda dinonaktifkan.');
        }

        session()->set([
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'nama'       => $user['nama_lengkap'],
            'role'       => $user['role'],
            'logged_in'  => true,
        ]);

        return redirect()->to($this->getRedirectUrl());
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to($this->getRedirectUrl());
        }
        return view('auth/register');
    }

    public function doRegister()
    {
        $rules = [
            'username'         => 'required|min_length[3]|is_unique[users.username]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'nama_lengkap'     => 'required|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username'     => $this->request->getPost('username'),
            'email'        => $this->request->getPost('email'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role'         => 'user',
            'status'       => 'aktif',
        ];

        $this->userModel->insert($data);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }

    private function getRedirectUrl()
    {
        $role = session()->get('role');
        return $role === 'admin' ? '/admin/dashboard' : '/user/kategori';
    }
}
