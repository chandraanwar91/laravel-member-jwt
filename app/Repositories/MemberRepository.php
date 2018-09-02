<?php namespace App\Repositories;

use App\Repositories\AbstractRepository;
use App\Repositories\Contracts\MemberInterface;
use App\Models\Member;
use App\Models\MemberProfile;

class MemberRepository implements MemberInterface{

    public function __construct(Member $member,MemberProfile $memberProfile)
    {
        $this->model = $member;
        $this->modelProfile = $memberProfile;
    }


    /**
     * Create user member with given data
     *
     * @param $data
     *
     * @return static
     */
    public function create(array $data)
    {
        $user = $this->findByEmail($data['email']);

        if(!empty($user)){
            return false;
        }

        $memberProfile = $this->modelProfile->create($data);

        $data['profile_id'] = $memberProfile->id ?? 1;

        $user = $this->model->create($data);

        return $user;
    }

     /**
     * Return member info by email
     *
     * @param $email
     */
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Return member info by email
     *
     * @param $email
     */
    public function findByEmailAndResetToken($email,$token)
    {
        return $this->model->where('email', $email)->where('reset_token', $token)->first();
    }

    /**
     * Return member info by email
     *
     * @param $email
     */
    public function findByEmailAndVerificationToken($email,$token)
    {
        return $this->model->where('email', $email)->where('verification_token', $token)->first();
    }

    /**
     * Return list member
     *
     * @param $param
     */
    public function findAllWithPaginate(array $param)
    {
        $perpage = $param['perpage'] ?? 10;
        $keyword = $param['keyword'] ?? '';
        $members = Member::orderBy('created_at', 'desc');

        if (!empty($keyword)) {
            if (strpos($keyword, '@')) {
                $members->where('email', $keyword);
            } else {
                $members->where('nama', 'like', '%' . $keyword . '%');
            }
        }

        $members = $members->paginate($perpage);

        return $members;
    }

    /**
     * Update Member
     *
     * @param $id
     */
    public function update(array $request,$id)
    {
        $member = $this->findById($id);

        unset($request['email']);
        $member->update($request);
        $member->profile->update($request);

        $member = $member->fresh();

        return $member;

    }

    /**
     * Delete Member
     *
     * @param $id
     */
    public function delete($id)
    {
        $member = $this->findById($id);

        $member->delete();

        return true;

    }

    /**
     * change Member Status
     *
     * @param $id
     */
    public function changeStatus($id)
    {
        $status = 'active';
        $member = $this->findById($id);
        if($member->status == 'active'){
            $status = 'suspend';
        }
        $member->update(['status' => $status]);

        $member = $member->fresh();

        return $member;

    }

    /**
     * Return member by id
     *
     * @param $id
     */
    public function findById($id)
    {
        $member = Member::findOrFail($id);

        return $member;
    }
}