<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'show', 'verify', 'resend', 'forgotPassword', 'recoveryPassword']]);
        $this->middleware('verified', ['except' => ['register', 'show', 'verify', 'resend', 'forgotPassword', 'recoveryPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get a JWT via given credentials.
     * @OA\Post (
     *     path="/auth/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                required={"email", "password"},
     *                @OA\Property(property="email", type="string", example="user@email.com"),
     *                @OA\Property(property="password", type="string", example="password"),
     *             )
     *         )
     *      ),
     *           @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                     @OA\Property(property="access_token", type="string", example="Token jwt"),
     *                     @OA\Property(property="token_type", type="string", example="Bearer"),
     *                     @OA\Property(property="expires_in", type="number", example="3600"),
     *         )
     *     ),
     *
     *      @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Wrong username and/or password"),))
     * )
     */
    public function login(Request $req)
    {
        $credentials = $req->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Wrong username and/or password'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Register account
     * @OA\Post (
     *     path="/auth/register",
     *     operationId="register",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                required={"name", "email", "password"},
     *                @OA\Property(property="name", type="string", example="User name"),
     *                @OA\Property(property="email", type="string", example="user@email.com"),
     *                @OA\Property(property="password", type="string", example="password"),
     *             )
     *         )
     *      ),
     *           @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *                     @OA\Property(property="message", type="string", example="Registered successfully. Please check your email to verify your account."),
     *         )
     *     ),
     *    @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *                      @OA\Property(property="status", type="number", example="400"),
     *                    @OA\Property(property="message", type="string", example="Oops we have detected errors"),
     * @OA\Property(
     *                 type="array",
     *                 property="errors",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="name", type="array",
     *                         @OA\Items(
     *                          example="The name field is required."
     *                            )
     *                     ),
     *                     @OA\Property(property="email", type="array",
     *                         @OA\Items(
     *                          example="The email field is required."
     *                            )
     *                     ),
     *                     @OA\Property(property="password", type="array",
     *                         @OA\Items(
     *                          example="The email field is required."
     *                            )
     *                     ),
     *                 )
     *             )
     *                 )
     *         ),
     * )
     */

    public function register(Request $req)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ];

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            $errors = [
                'status' => 400,
                'message' => 'Oops we have detected errors',
                'errors' => $validator->errors(),
            ];

            return response()->json($errors, 400);
        }

        $user = User::create([
            'name' => e($req->input('name')),
            'email' => e($req->input('email')),
            'password' => bcrypt($req->input('password')),
            'confirmation_token' => Str::random(60),
        ]);

        return response()->json(['message' => 'Registered successfully. Please check your email to verify your account.'], 201);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get the authenticated User.
     * @OA\Get (
     *     security={{"Bearer":{}}},
     *     path="/auth/me",
     *     operationId="me",
     *     tags={"Auth"},
     *           @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                     @OA\Property(property="id", type="number", example="1"),
     *                     @OA\Property(property="name", type="string", example="User name"),
     *                     @OA\Property(property="email", type="string", example="user@email.com"),
     *         )
     *     ),
     *
     *      @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"),))
     * )
     */
    public function me()
    {
        // return response()->json($req->user(),200);
        return response()->json(auth()->user(), 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Log the user out (Invalidate the token).
     * @OA\Post (
     *     security={{"Bearer":{}}},
     *     path="/auth/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *           @OA\Response(response=200, description="Ok", @OA\JsonContent(@OA\Property(property="message", type="string", example="Successfully logged out"),)),
     *           @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"),))
     * )
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Refresh a token
     * @OA\Post (
     *     security={{"Bearer":{}}},
     *     path="/auth/refresh",
     *     operationId="refresh",
     *     tags={"Auth"},
     *           @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *                     @OA\Property(property="access_token", type="string", example="Token jwt"),
     *                     @OA\Property(property="token_type", type="string", example="Bearer"),
     *                     @OA\Property(property="expires_in", type="number", example="3600"),
     *         )
     *     ),
     *           @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"),))
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Check if the account is verified
     * @OA\Get (
     *     path="/auth/email/verify",
     *     operationId="verify-email",
     *     tags={"Auth"},
     *            @OA\Response(response=200, description="Ok", @OA\JsonContent(@OA\Property(property="message", type="string", example="Please verify your email address."),)),
     *            @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"),))
     * )
     */
    public function show()
    {
        return response()->json(['message' => 'Please verify your email address.']);
    }

    /**
     * Verify the user's email address.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Verify the user's email address.
     * @OA\Get (
     *     path="/auth/email/verify/{id}/{hash}",
     *     tags={"Auth"},
     *     operationId="email-verify-id-hash",
     *     @OA\Parameter(in="path", name="id", required=true,@OA\Schema(type="number")),
     *     @OA\Parameter(in="path", name="hash", required=true,@OA\Schema(type="string")),
     *            @OA\Response(response=202, description="Validation if the user is already verified", @OA\JsonContent(@OA\Property(property="message", type="string", example="Email already verified."),)),
     *            @OA\Response(response=200, description="Ok", @OA\JsonContent(@OA\Property(property="message", type="string", example="Email verified successfully."),)),
     *            @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"),))
     * )
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }

    /**
     * Reenvía el correo electrónico de verificación de correo electrónico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Check if the account is verified
     * @OA\Post (
     *     path="/auth/resend",
     *     operationId="resend",
     *     tags={"Auth"},
     *           @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(@OA\Property(type="object", @OA\Property(property="email", type="string")),
     *                 example={"email":"user@email.com"}
     *             )
     *         )
     *      ),
     *            @OA\Response(response=200, description="Ok", @OA\JsonContent(@OA\Property(property="message", type="string", example="A new verification link has been emailed to you!"),)),
     *            @OA\Response(response=404, description="Not Found", @OA\JsonContent(@OA\Property(property="message", type="string", example="We were unable to find a user with that email address"),)),
     *            @OA\Response(response=400, description="Bad Request", @OA\JsonContent(@OA\Property(property="message", type="string", example="This email address is already verified"),))
     * )
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'We were unable to find a user with that email address.',
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'This email address is already verified.',
            ], 400);
        }

        $user->resendEmailVerificationNotification();

        return response()->json([
            'status' => 200,
            'message' => 'A new verification link has been emailed to you!',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/auth/forgot-password",
     *     summary="Request password reset",
     *     operationId="forgot-password",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Ok", @OA\JsonContent(@OA\Property(property="message", type="string", example="Password reset email sent."),)),
     *     @OA\Response(response=500, description="Internal server error", @OA\JsonContent(@OA\Property(property="message", type="string", example="The password reset email could not be sent."),)),
     * )
     */
    public function forgotPassword(Request $request)
    {
        $rules = ['email' => 'required|email'];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = [
                'status' => 400,
                'message' => 'Oops we have detected errors',
                'errors' => $validator->errors(),
            ];

            return response()->json($errors, 400);
        }

        $response = Password::sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
        ? response()->json(['message' => 'Password reset email sent.'], 200)
        : response()->json(['error' => 'The password reset email could not be sent.'], 500);
    }

    /**
     * @OA\Post(
     *     path="/auth/recovery/password?token={token}&email={email}",
     *     summary="Recovery the password using a recovery token.",
     *     operationId="recovery-password",
     *     tags={"Auth"},
     *     @OA\Parameter(in="path", name="token", required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(in="path", name="email", required=true,@OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Contraseña nueva del usuario registrado.",
     *         @OA\JsonContent(
     *             required={"password", "password_confirmation"},
     *             @OA\Property(property="password", type="string", format="password", example="mypassword"),
     *             @OA\Property(property="password_confirmation", type="string", format="password",example="mypassword"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Ok", @OA\JsonContent(@OA\Property(property="message", type="string", example="Password changed successfully."),)),
     *     @OA\Response(response=400, description="Bad Request", @OA\JsonContent(@OA\Property(property="message", type="string", example="Could not change password."),)),
     * )
     */

    public function recoveryPassword(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();
        });

        if ($response == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password changed successfully.'], 200);
        } else {
            return response()->json(['message' => 'Could not change password.'], 400);
        }
    }
}
