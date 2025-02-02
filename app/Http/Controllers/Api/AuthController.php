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
use Illuminate\Http\Request;
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
            ], __('auth.register_successful'), Response::HTTP_CREATED);

        } catch(QueryException $e) {
            DB::rollBack();
            return CommonUtil::errorResponse(__('auth.registration_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            DB::rollBack();
            return CommonUtil::errorResponse(__('auth.registration_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        /**
     * Refresh user token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(): JsonResponse
    {
        try {

            $new_token = JWTAuth::refresh(JWTAuth::getToken());

            return CommonUtil::successResponse([
                'token' => $new_token
            ], __('auth.succesfull_new_token'));

        } catch (Exception $e) {
            return CommonUtil::errorResponse(__('auth.refresh_tokent_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
  public function logout(Request $request): JsonResponse
  {
      try {

        JWTAuth::invalidate(JWTAuth::getToken());

        return CommonUtil::successResponse([
          'user' => new UserResource($request->user())
        ], __('auth.succesfull_logout'));

    } catch (Exception $e) {
      return CommonUtil::errorResponse(__('auth.logout_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

}
