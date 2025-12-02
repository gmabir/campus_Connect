<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class DashboardController extends Controller
    {
        public function __construct()
        {
            $this->middleware('auth');
        }
        
        // This function handles the central /dashboard route and redirects
        public function index()
        {
            $user = Auth::user();

            if ($user->role == '2') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role == '1') {
                return redirect()->route('teacher.dashboard');
            }
            
            return redirect()->route('student.dashboard'); // Default to student
        }
    }