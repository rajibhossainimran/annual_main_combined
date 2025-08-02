<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Setting;
use App\Providers\RouteServiceProvider;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        AuditService::AuditLogEntry(AuditModel::User,OperationTypes::Login,auth()->user()->name.' logged In',null,null,auth()->user()->id);

        $setWarningDays = Setting::where('key','Warning Days')->select('value')->first();
        $setExpireDays = Setting::where('key','Reset Days')->select('value')->first();
        $warningDays = 0;
        if(isset($setExpireDays)){
            $warningDays =  $setExpireDays->value - $setWarningDays->value;
        }

        if(isset(Auth::user()->change_password)){
            $updatedDate = date('Y-m-d', strtotime(Auth::user()->change_password));
            $updatedDateWith = date('Y-m-d', strtotime($updatedDate. ' + '.$setExpireDays->value.' days'));
            $updatedDateWithWarning = date('Y-m-d', strtotime($updatedDate. ' + '.$warningDays.' days'));
            $currentDate = date('Y-m-d');
            if($currentDate > $updatedDateWith){
                \Session::put('expiredId', Auth::user()->id);

                Auth::guard('web')->logout();

                // $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect('/change-password/expire/')->with('error', 'Your password has been expired.');
            }elseif ($currentDate > $updatedDateWithWarning){
                return redirect('/dashboard')->with('error', 'Please change your password as soon as possible.');
            }
        }else{
            $updatedDate = date('Y-m-d', strtotime(Auth::user()->created_at));
            $updatedDateWith = date('Y-m-d', strtotime($updatedDate. ' + '.$setExpireDays->value.' days'));
            $updatedDateWithWarning = date('Y-m-d', strtotime($updatedDate. ' + '.$warningDays.' days'));
            $currentDate = date('Y-m-d');
            if($currentDate > $updatedDateWith){
                \Session::put('expiredId', Auth::user()->id);
                Auth::guard('web')->logout();

                // $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect('/change-password/expire/')->with('error', 'Your password has been expired.');
            }elseif ($currentDate > $updatedDateWithWarning){
                return redirect('/dashboard')->with('error', 'Please change your password as soon as possible.');
            }
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        AuditService::AuditLogEntry(AuditModel::User,OperationTypes::Logout,auth()->user()->name.' logged Out',null,null,auth()->user()->id);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
