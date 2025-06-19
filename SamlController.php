<?php

namespace App\Http\Controllers;

use App\Models\User;
use OneLogin\Saml2\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Storage;

class SamlController extends Controller
{
    private function getSamlAuth(): Auth
    {
        $settings = require base_path('app/Saml/settings.php');
        return new Auth($settings);
    }

    public function login()
    {
        $auth = $this->getSamlAuth();
        return redirect($auth->login());
    }

    public function acs(Request $request)
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

        // Read about data from file
        $about = json_decode(Storage::get('about/content.text') ?? '{}');


        $auth = $this->getSamlAuth();
        $auth->processResponse();

        if (!$auth->isAuthenticated()) {
            abort(401, 'SAML Authentication failed');
        }

        // You can get user data here
        $samlUserData = $auth->getAttributes();
        $userEmail = $auth->getNameId();

        $user = User::where('email', $userEmail)->first();

        if (!$user) {
            return view('auth.login', compact('about'));
        }

        FacadesAuth::login($user);

        return redirect()->route('admin.dashboard');
    }

    public function metadata()
    {
        $auth = $this->getSamlAuth();
        $metadata = $auth->getSettings()->getSPMetadata();

        header('Content-Type: application/xml');
        return response($metadata);
    }

    public function logout()
    {
        $auth = $this->getSamlAuth();
        return redirect($auth->logout());
    }
}
