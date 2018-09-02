<?php namespace App\Repositories\Contracts;

interface MemberInterface
{
    //public function all();

    public function create(array $data);

    public function findById($id);
}