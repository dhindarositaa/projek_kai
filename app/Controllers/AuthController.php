<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
    }

    public function register()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    public function processRegister()
    {
        // Pastikan AJAX (frontend kita kirim X-Requested-With)
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Method not allowed'
            ]);
        }

        // Ambil input
        $name = trim($this->request->getPost('name'));
        $email = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        $confirm = $this->request->getPost('confirm_password');
        $agree = $this->request->getPost('agree');

        // Validasi checkbox
        if (!$agree) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda harus menyetujui Privacy Policy'
            ]);
        }

        // Rules validasi
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|max_length[255]|is_unique[users.email]',
            'password' => 'required|min_length[6]|max_length[255]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $dataToSave = [
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT)
        ];

        try {
            $insertId = $this->userModel->insert($dataToSave);

            if ($insertId === false) {
                $modelErrors = $this->userModel->errors();
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data',
                    'errors' => $modelErrors ? $modelErrors : ['database' => 'Insert gagal']
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registrasi berhasil! Silakan login.'
            ]);

        } catch (\Exception $e) {
            // Jangan expose stack trace di production, tapi untuk debugging bisa ditampilkan sementara
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    public function checkEmail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Method not allowed'
            ]);
        }

        $email = $this->request->getPost('email');
        if (empty($email)) {
            return $this->response->setJSON([
                'exists' => false,
                'message' => 'Email kosong'
            ]);
        }

        $user = $this->userModel->where('email', $email)->first();

        return $this->response->setJSON([
            'exists' => $user ? true : false,
            'message' => $user ? 'Email sudah terdaftar' : 'Email tersedia'
        ]);
    }

    // proses login (tetap) — disesuaikan jika butuh
    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

public function processLogin()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(405)->setJSON([
            'status' => 'error',
            'message' => 'Method not allowed'
        ]);
    }

    $email = trim($this->request->getPost('email'));
    $password = $this->request->getPost('password');

    if (empty($email) || empty($password)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Email dan password harus diisi'
        ]);
    }

    $user = $this->userModel->where('email', $email)->first();

    // Tidak ditemukan user
    if (!$user) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Email atau password salah'
        ]);
    }

    // Ambil hash password dari kolom yang ada (support legacy 'password' atau 'password_hash')
    $hash = $user['password_hash'] ?? $user['password'] ?? null;

    if (empty($hash)) {
        // Catat ke log supaya bisa diperiksa (tidak menampilkan detail ke user)
        log_message('error', 'Login error: password hash column missing for user id ' . ($user['id'] ?? 'unknown'));
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Terjadi kesalahan pada server (auth). Silakan hubungi administrator.'
        ]);
    }

    // Verifikasi password
    if (!$this->userModel->verifyPassword($password, $hash)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Email atau password salah'
        ]);
    }

    // Set session
    $sessionData = [
        'userId' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'isLoggedIn' => true
    ];
    $this->session->set($sessionData);

    return $this->response->setJSON([
        'status' => 'success',
        'message' => 'Login berhasil!',
        'redirect' => site_url('/input-manual')
    ]);
}

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }
}
