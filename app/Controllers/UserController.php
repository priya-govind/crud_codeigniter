<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
     public function index()
    {
        return view('users/index');
    }

    public function show($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        return view('users/show', ['user' => $user]);
    }

    public function edit($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        return view('users/edit', ['user' => $user]);
    }

    public function update($id)
    {
        $userModel = new UserModel();
        $userModel->update($id, [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ]);

        return redirect()->to('/users');
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id);

        return redirect()->to('/users');
    }
    public function getUsers()
{
    $request = service('request');
    $userModel = new UserModel();

    // DataTables parameters
    $draw   = $request->getVar('draw');
    $start  = $request->getVar('start');
    $length = $request->getVar('length');
    $search = $request->getVar('search')['value'];

    // Base query
    $builder = $userModel->builder();

    if ($search) {
        $builder->like('name', $search)
                ->orLike('email', $search);
    }

    $totalRecords = $userModel->countAllResults(false);
    $builder->limit($length, $start);
    $users = $builder->get()->getResultArray();

    return $this->response->setJSON([
        'draw'            => intval($draw),
        'recordsTotal'    => $userModel->countAll(),
        'recordsFiltered' => $totalRecords,
        'data'            => $users,
    ]);
}
}
