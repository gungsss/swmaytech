<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::doRegister');
$routes->get('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('dashboard', 'Admin::dashboard');

    $routes->get('riwayat', 'Admin::riwayat');
    $routes->get('riwayat/detail/(:num)/(:num)', 'Admin::detailRiwayat/$1/$2');

    $routes->get('kategori', 'Admin::kategori');
    $routes->get('kategori/tambah', 'Admin::tambahKategori');
    $routes->post('kategori/simpan', 'Admin::simpanKategori');
    $routes->get('kategori/edit/(:num)', 'Admin::editKategori/$1');
    $routes->post('kategori/update/(:num)', 'Admin::updateKategori/$1');
    $routes->get('kategori/hapus/(:num)', 'Admin::hapusKategori/$1');

    $routes->get('soal', 'Admin::soal');
    $routes->get('soal/tambah', 'Admin::tambahSoal');
    $routes->post('soal/simpan', 'Admin::simpanSoal');
    $routes->get('soal/edit/(:num)', 'Admin::editSoal/$1');
    $routes->post('soal/update/(:num)', 'Admin::updateSoal/$1');
    $routes->get('soal/hapus/(:num)', 'Admin::hapusSoal/$1');

    $routes->get('users', 'Admin::users');
    $routes->get('users/toggle/(:num)', 'Admin::toggleUserStatus/$1');
    $routes->get('users/change-password/(:num)', 'Admin::changePassword/$1');
    $routes->post('users/update-password/(:num)', 'Admin::updatePassword/$1');
});

$routes->group('user', ['filter' => 'auth:user'], function ($routes) {
    $routes->get('kategori', 'User::kategori');
    $routes->get('dashboard', 'User::dashboard');
    $routes->get('dashboard/(:num)', 'User::dashboard/$1');
    $routes->get('soal/kerjakan/(:num)', 'User::kerjakan/$1');
    $routes->post('soal/simpan-jawaban', 'User::simpanJawaban');
    $routes->get('riwayat', 'User::riwayat');
});
