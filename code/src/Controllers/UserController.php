<?php

namespace src\Controllers;
class UserController
{
    public function index()
    {
        $users = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob']
        ];
        echo json_encode($users);
    }

    public function show($id)
    {
        $user = ['id' => $id, 'name' => 'User ' . $id];
        echo json_encode($user);
    }
}