<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function index()
    {
       return view('login');
    }

     public function register()
    {
       return view('register');
    }

    public function store()
    {
        $rules = [
            'name'             => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'Please enter your name',
                    'min_length' => 'Name must be at least 3 characters long'
                ]
            ],
            'email'            => [
                'rules'  => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required'    => 'Email is required',
                    'valid_email' => 'Please enter a valid email address',
                    'is_unique'   => 'This email is already registered'
                ]
            ],
            'password'         => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'Password is required',
                    'min_length' => 'Password must be at least 6 characters long'
                ]
            ],
            'confirm_password' => [
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Please confirm your password',
                    'matches'  => 'Passwords must match'
                ]
            ],
        ];

        if (! $this->validate($rules)) {
            return view('register', [
                'validation' => $this->validator
            ]);
        }

        $userModel = new UserModel();
        $userModel->save([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/login')->with('success','Registration successful');
    }


    public function login()
    {
        return view('login');
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (! $this->validate($rules)) {
            return view('login', [
                'validation' => $this->validator
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email',$this->request->getPost('email'))->first();

        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
            session()->set(['user_id'=>$user['id'],'user_name'=>$user['name']]);
            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error','Invalid credentials');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
