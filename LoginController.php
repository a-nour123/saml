<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Container;
use LdapRecord\Connection;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OneLogin\Saml2\Auth as SamlAuth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    use ThrottlesLogins;
    public $connection;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $maxAttempts = 3; // Maximum number of login attempts
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function LdapConnection()
    {
        // Split the DN string by commas
        $base_dn = explode(",", getLdapValue('LDAP_DEFAULT_BASE_DN'));
        $firstDcValue = null;
        foreach ($base_dn as $component) {
            if (strpos($component, "DC=") === 0) {
                // Extract the value of the first "DC" component
                $firstDcValue = substr($component, 3);
                break;
            }
        }

        $connection = new Connection([
            'hosts' => explode(',', getLdapValue('LDAP_DEFAULT_HOSTS')),
            'port' => getLdapValue('LDAP_DEFAULT_PORT'),
            'base_dn' => getLdapValue('LDAP_DEFAULT_BASE_DN'),
            'username' =>  getLdapValue('LDAP_DEFAULT_USERNAME'),
            // 'password' => getLdapValue('LDAP_DEFAULT_PASSWORD'),
            'password' =>  base64_decode(getLdapValue('LDAP_DEFAULT_PASSWORD')),
            // Optional Configuration Options
            'use_ssl'          => (getLdapValue('LDAP_DEFAULT_SSL') == 'true') ? true : false,
            'use_tls'          => (getLdapValue('LDAP_DEFAULT_TLS') == 'true') ? true : false,
            'version'          => (int)getLdapValue('LDAP_DEFAULT_VSERSION'),
            'timeout'          => (int)getLdapValue('LDAP_DEFAULT_TIMEOUT'),
            'follow_referrals' => (getLdapValue('LDAP_DEFAULT_Follow') == 'true') ? true : false,
        ]);

        try {
            $connection->connect();
            $container = Container::addConnection($connection);
            $this->connection = $connection;
            $this->container = $container;
        } catch (\LdapRecord\Auth\BindException $e) {

            $error = $e->getDetailedError();

            echo $error->getErrorCode();
            echo $error->getErrorMessage();
            echo $error->getDiagnosticMessage();
        }
    }


    protected function attemptLogin(Request $request)
    {
        // $password = ($request->password);
        $password = base64_decode($request->password);

        // \Auth::logoutOtherDevices($password);
        $check_user = User::where('username', $request->username)->first();
        if ($check_user) {
            $base_dn = explode(",", getLdapValue('LDAP_DEFAULT_BASE_DN'));
            $firstDcValue = null;
            $this->LdapConnection();
            if ($this->connection) {
                $user = $this->connection->query()->where('samaccountname', '=', $request->username)->first();

                if ($user) {
                    $dn = $user['distinguishedname'][0];
                    $components = explode(",", $dn);
                    // Initialize the variable for the first DC

                    // Iterate through the components to find the first DC
                    foreach ($components as $component) {
                        if (strpos($component, "DC=") === 0) {
                            // Extract the value of the first "DC" component
                            $firstDcValue = substr($component, 3);
                            break;
                        }
                    }

                    $authUser = $this->connection->auth()->attempt($firstDcValue . '\\' . $request->username,  $password);

                    if ($authUser) {
                        $check_user->update([
                            'password' => Hash::make($password)
                        ]);
                        return $this->guard()->attempt($this->credentials($request), $request->boolean('remember'));
                    } else {
                        return false;
                    }
                } else {
                    return $this->guard()->attempt($this->credentials($request), $request->boolean('remember'));
                }
            } else {
                return $this->guard()->attempt($this->credentials($request), $request->boolean('remember'));
            }
        } else {
            return false;
        }
    }



    public function username()
    {
        return 'username';
    }
    public function redirectTo()
    {
        return route('admin.dashboard');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $request['password'] =  base64_decode($request->password);
        return array_merge($request->only($this->username(), 'password'), ['enabled' => 1]);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (!Storage::exists('about/content.text')) {
            $data['vision'] = '';
            $data['message'] = '';
            $data['mission'] = '';
            $data['objectives'] = '';
            $data['responsibilities'] = '';

            // Store temporary about data to file
            Storage::put('about/content.text', json_encode($data));
        }
        // // Read about data from file
        $about = json_decode(Storage::get('about/content.text'));
        return view('auth.login', compact('about'));
    }
    protected function sendLockoutResponse(Request $request)
    {
        $this->lockAccount($request);

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => $this->getLockoutErrorMessage(),
            ]);
    }

    protected function lockAccount(Request $request)
    {
        $user = $this->getUserByCredentials($request->only($this->username()));

        if ($user) {
            $user->update(['enabled' => 0]);
        }
    }

    protected function getLockoutErrorMessage()
    {
        return __('auth.locked');
    }

    // ...

    protected function getUserByCredentials(array $credentials)
    {
        return Auth::getProvider()->retrieveByCredentials($credentials);
    }

    public function logout()
    {
        Auth::logout();
        $auth = $this->getSamlAuth();
        $auth->logout();
        return redirect()->route('saml.login');
    }

      private function getSamlAuth(): SamlAuth
    {
        $settings = require base_path('app/Saml/settings.php');
        return new SamlAuth($settings);
    }
}

