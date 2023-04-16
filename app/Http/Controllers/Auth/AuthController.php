<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

// use App\Mail\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Notifications\MyVerifyEmailNotification;
use App\Notifications\VerifyEmailNotificationRegister;


class AuthController extends Controller
{
      /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'show', 'verify', 'resend']]);
        $this->middleware('verified', ['except' => ['register', 'show', 'verify', 'resend']]);
    }


  /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $req)
    {
        $credentials = $req->only(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

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
            'name' => $req->input('name'),
            'email' => $req->input('email'),
            'password' => bcrypt($req->input('password')),
            'confirmation_token' => Str::random(60)
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Registered successfully. Please check your email to verify your account.'], 201);


    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        // return response()->json($req->user(),200);
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
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
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function show()
    {
        return response()->json(['message' => 'Please verify your email address.']);
    }

    /**
     * Verifica la dirección de correo electrónico del usuario.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
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
    // public function resend(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user->hasVerifiedEmail()) {
    //         return response()->json(['message' => 'Your email address has already been verified...'], 400);
    //     }

    //     $user->sendEmailVerificationNotification();

    //     return response()->json(['message' => 'An email verification message has been sent.'], 200);
    // }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'We were unable to find a user with that email address.'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'This email address is already verified.'
            ], 422);
        }

        $user->resendEmailVerificationNotification();

        return response()->json([
            'status' => 200,
            'message' => 'A new verification link has been emailed to you!'
        ], 200);
    }
}
