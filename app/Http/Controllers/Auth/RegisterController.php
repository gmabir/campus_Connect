<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\Rule;
    use Illuminate\Validation\Rules;

    class RegisterController extends Controller
    {
        public function showRegistrationForm()
        {
            return view('auth.register');
        }

        public function register(Request $request)
        {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                // Added validation for the role (0=Student, 1=Teacher, 2=Admin)
                'role' => ['required', 'string', Rule::in(['0', '1', '2'])],
                // super_key is required ONLY if role is '2' (Admin)
                'super_key' => ['nullable', Rule::requiredIf($request->input('role') == '2'), 'string'],
            ]);

            // **Admin Super Key Check**
            if ($request->role === '2' && $request->super_key !== env('ADMIN_SUPER_KEY')) {
                return back()->withInput()->withErrors(['super_key' => 'The provided Super Key is invalid for Admin registration.']);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role, // Store the selected role
            ]);

            Auth::login($user);

            return redirect()->route('dashboard'); // Redirect to a centralized dashboard route
        }
    }