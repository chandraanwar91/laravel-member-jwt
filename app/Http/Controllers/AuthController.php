<?php

namespace App\Http\Controllers;

use App\Events\MemberRegistered;
use App\Jobs\SendResetPasswordEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Models\Member;
use App\Models\MemberProfile;

##Repository
use App\Repositories\MemberRepository;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class AuthController extends ApiController
{

    protected $model;

    public function __construct(Member $member,MemberProfile $memberProfile)
    {
       $this->model = new MemberRepository($member,$memberProfile);
    }

    /**
     * Register Member
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($request->all(), $this->registerRules());

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        $memberData = $request->all();
        $memberData['verification_token'] = generateUUID();

        $member = $this->model->create($memberData);
        $token = JWTAuth::fromUser($member->refresh());
        //uncomment if only configuration mail already set
        //event(new MemberRegistered($member));

        // Trying to prevent clock skew
        sleep(1);

        return $this->respond(compact('token'));
    }

    /**
     * Login member
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($request->all(), $this->loginRules());

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        try {
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return $this->respondFailValidation('Invalid email or password');
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], 500);
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], 500);
        } catch (JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], 500);
        }

        // Trying to prevent clock skew
        sleep(1);

        return $this->respond(compact('token'));
    }

    /**
     * Send password reset email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($request->all(), ['email' => 'required|email']);

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        $member = $this->model->findByEmail($request->email);

        if ($member) {
            $member->reset_token = generateUUID();
            $member->save();
            //uncomment if only configuration mail already set
            //dispatch(new SendResetPasswordEmail($member));
        }

        return $this->respond(['status' => 'success']);
    }

    /**
     * Validate email and token combination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkResetToken(Request $request)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($request->all(), ['email' => 'required|email','token' => 'required']);

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        $member = $this->model->findByEmailAndResetToken($request->email,$request->token);

        if (!$member) {
            return $this->respondFailValidation('Invalid token');
        }

        return $this->respond(['status' => 'success']);
    }

    /**
     * Reset member password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email','token' => 'required']);

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        $member = $this->model->findByEmailAndResetToken($request->email,$request->token);

        if (!$member) {
            return $this->respondFailValidation('Invalid token');
        }

        $member->password = app('hash')->make($request->password);
        $member->reset_token = null;
        $member->save();

        return $this->respond(['status' => 'success']);
    }

    /**
     * Validate email and token combination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkVerificationToken(Request $request)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($request->all(), ['email' => 'required|email','token' => 'required']);

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        $member = $this->model->findByEmailAndVerificationToken($request->email,$request->token);

        if (!$member) {
            return $this->respondFailValidation('Invalid token');
        }

        return $this->respond(['status' => 'success']);
    }

    /**
     * Reset member password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email','token' => 'required']);

        if ($validator->fails()) {
            return $this->respondFailValidation($validator->errors()->toArray());
        }

        $member = $this->model->findByEmailAndVerificationToken($request->email,$request->token);

        if (!$member) {
            return $this->respondFailValidation('Invalid token');
        }

        $member->verification_token = null;
        $member->authentication = Member::AUTHENTICATION_VALID;
        $member->save();

        return $this->respond(['status' => 'success']);
    }

    /**
     * Register rules for form validation
     *
     * @return array
     */
    private function registerRules()
    {
        return [
            'fullname' => 'required',
            'password' => 'required|min:6',
            'email'    => 'required|email|unique:members',
        ];
    }

    /**
     * Register rules for form validation
     *
     * @return array
     */
    private function loginRules()
    {
        return [
            'email'    => 'required|email',
            'password' => 'required',
        ];
    }
}
