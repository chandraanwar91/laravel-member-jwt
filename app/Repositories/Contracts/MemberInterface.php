<?php namespace App\Repositories\Contracts;

interface MemberInterface
{
    //public function all();

    public function create(array $data);

    public function findByEmail($email);

    public function findByEmailAndResetToken($email,$resetToken);

    public function findByEmailAndVerificationToken($email,$confirmToken);

    public function findAllWithPaginate(array $param);

    public function update(array $data,$id);

    public function delete($id);

    public function changeStatus($id);
}