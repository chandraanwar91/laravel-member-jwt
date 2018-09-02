<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberProfile;
use App\Transformers\MemberTransformer;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Manager;

##Repository
use App\Repositories\MemberRepository;

/**
 * Class MemberController
 *
 * @package App\Http\Controllers
 */
class MemberController extends ApiController
{
    protected $model;

    public function __construct(Member $member,MemberProfile $memberProfile,Manager $fractal,Request $request)
    {
        parent::__construct($fractal, $request);
       $this->model = new MemberRepository($member,$memberProfile);
    }


    /**
     * Get all records
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $members = $this->model->findAllWithPaginate($request->all());

       return $this->respondWithPagination($members, new MemberTransformer);
    }

    /**
     * Show the record
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $member = $this->model->findById($id);
        } catch (\Exception $e) {
            return $this->respondFailValidation('Member Not Found');
        }
       

        return $this->respondWithItem($member, new MemberTransformer);
    }

    /**
     * Store the record
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        $request->merge([
            'authentication'             => 'valid'
        ]);

        $member = $this->model->create($request->all());

        return $this->respondCreated();
    }

    /**
     * Update the record
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $rules = $this->rules($id);
        unset($rules['email']);
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        $member = $this->model->findById($id);
        
        $member = $this->model->update($request->all(),$id);

        return $this->respondWithItem($member, new MemberTransformer);
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $delete = $this->model->delete($id);

        return $this->respondDeleted();
    }

    /**
     * Update the record
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus($id)
    {
        $memberChangeStatus = $this->model->changeStatus($id);

        return $this->respondWithItem($memberChangeStatus, new MemberTransformer);
    }

    /**
     * Rules for form validation
     *
     * @param null $id
     * @return array
     */
    private function rules($id = null)
    {
        $rules = [
            'fullname'     => 'required',
            'email'        => 'required|email|unique:members' . ($id ? ',email,$id' : ''),
            'password'     => 'required',
        ];

        if (!$id) {
            $rules['password'] = '';
        }

        return $rules;
    }
}
