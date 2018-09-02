<?php namespace App\Repositories;

use App\Repositories\AbstractRepository;
use App\Models\MemberProfile;

class MemberProfileRepository extends AbstractRepository {

    public function __construct(MemberProfile $memberProfile)
    {
        $this->model = $memberProfile;
    }

    /**
     * Update or create user member Profile with given data
     *
     * @param $data
     *
     * @return static
     */
    public function create(array $data)
    {
        $memberProfile = $this->model->create($data);

        return $memberProfile;
    }


    /**
     * Return member profile info by id
     *
     * @param $email
     */
    public function findById($id)
    {
        return $this->model->find($id);
    }
}