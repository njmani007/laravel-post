<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Logging\CustomLogger;

class LoginRegisterController extends Controller
{
    protected $customLogger;

    /**
     * Instantiate a new LoginRegisterController instance.
     *
     * @param \App\Logging\CustomLogger $customLogger
     * @return void
     */
    public function __construct(CustomLogger $customLogger)
    {
        $this->middleware('guest')->except([
            'logout', 'dashboard','post.index'
        ]);

        $this->customLogger = $customLogger;
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        $pageTitle = "User Register";
        return view('auth.register', compact('pageTitle'));
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('login')
            ->withSuccess('You have successfully registered. Please log in!');
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        $pageTitle = "User Login";
        return view('auth.login',compact('pageTitle'));
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                // Log successful login
                $this->customLogger->processLogger([])->info('User logged in successfully: ' . Auth::user()->email);

                return redirect()->route('post.index')->with('success', 'Successfully logged in!');
            }

            // Log failed login attempt
            $this->customLogger->errorLogger([])->warning('User login failed: ' . $request->input('email'));

            return back()->withErrors([
                'email' => 'Your provided credentials do not match our records.',
            ])->onlyInput('email');
        } catch (\Exception $e) {
            // Log authentication error
            $this->customLogger->errorLogger([])->error('Authentication error: ' . $e->getMessage(), ['email' => $request->input('email')]);

            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }


    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if(Auth::check())
        {
            return view('auth.dashboard');
        }

        return redirect()->route('login')
            ->withErrors([
            'email' => 'Please login to access the dashboard.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');;
    }

}
