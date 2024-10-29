<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use App\Http\Requests\Api\UserRegisterRequest;
use App\Http\Resources\Api\UserResource;
use App\Repositories\User\UserInterface;
use App\Utils\Api\CommonUtil;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $user;

    public function __construct(UserInterface $user) {
        $this->user = $user;
    }

    /**
     * Authenticate the user and return a JWT token upon successful login.
     *
     * @param  \App\Http\Requests\Api\UserLoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
    */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!$token = JWTAuth::attempt($credentials)) {
          return CommonUtil::errorResponse(__('auth.failed'), Response::HTTP_UNAUTHORIZED);
        }

        return CommonUtil::successResponse([
            'token' => $token,
            'user' => new UserResource($request->user())
          ], __('auth.login_successful'));

    }

    /**
     * Register a new user and return a JWT token.
     *
     * @param  \App\Http\Requests\Api\UserRegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
    */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {

            DB::beginTransaction();

            $user = $this->user->create($data);

            $token = JWTAuth::fromUser($user);

            DB::commit();

            return CommonUtil::successResponse([
                'token' => $token,
                'user' => new UserResource($user)
            ], __('auth.register_successful'));

        } catch(QueryException $e) {
            DB::rollBack();
            return CommonUtil::errorResponse(__('auth.registration_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            return CommonUtil::errorResponse(__('auth.registration_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
