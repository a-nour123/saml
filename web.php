<?php

use App\Events\MessageSent;
use App\Events\NotificationDataRealTime;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\phishing\PhishingCampaignController;
use App\Http\Controllers\admin\phishing\PhishingDomainsController;
use App\Http\Controllers\admin\phishing\PhishingWebsitePageController;
use App\Http\Controllers\SamlController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotificationController;
use App\Models\PhishingTemplate;
use App\Models\PhishingWebsitePage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth']], function () {
    // locale Route
    Route::get('lang/{locale}', [LanguageController::class, 'swap'])->name('language.swap');
    Route::get('/admin/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::redirect('/admin', '/admin/dashboard');
    Route::Post('/admin/dashboard/GetFrameworkAuditGraph', [DashboardController::class, 'GetFrameworkAuditGraph'])->name('admin.dashboard.GetFrameworkAuditGraph');
    Route::get('/document-compliance-audit-result', [DashboardController::class, 'documentComplianceAudit'])->name('document.compliance.auditpolicy.result');

    Route::get('/notification/read/{id}', [NotificationController::class, 'notificationMakeRead'])->name('notification-read');
    Route::get('/notifications', [NotificationController::class, 'notificationMore'])->name('notifications.more');
});

// Authentication Routes...
Route::get('check/email/admin', [DashboardController::class, 'user_check']);
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('testmail', [PhishingCampaignController::class, 'testmail'])->name('testmail');



// Route::get('PWPI/{id}', function ($id) {
//     $website = PhishingWebsitePage::find($id);
//     return view('admin.content.phishing.websites.website', get_defined_vars());
// });


// Route::domain('{subdomain}.{domain}')->group(function () {
//     Route::get('{id}', function ($subdomain,$domain,$id) {
//         $website = PhishingWebsitePage::find($id);
//         return view('admin.content.phishing.websites.website', get_defined_vars());
//     });
// });


Route::get('PWPI/{id}/click', [PhishingCampaignController::class, 'clickOnLink'])->name('phishing.clickOnLink');

Route::get('mail-opened', [PhishingCampaignController::class, 'mailOpened'])->name('mail.opened');
Route::get('mailForm-submited', [PhishingCampaignController::class, 'mailFormSubmited'])->name('mailForm.submited');
Route::get('mail-attachments', [PhishingCampaignController::class, 'mailAttachmentDownloaded'])->name('mailAttachments.download');
Route::get('show/{name}/{id}', [PhishingWebsitePageController::class,'show'])->name('website.show');

Route::get('/admin/horizontalMenu.json', function () {
    $path = resource_path('data/menu-data/horizontalMenu.json');

    // Check if the file exists
    if (!File::exists($path)) {
        return response()->json(['error' => 'File not found'], 404);
    }

    // Get the contents of the JSON file
    $json = File::get($path);

    // Decode the JSON data to ensure it's valid
    $data = json_decode($json);

    // Return the JSON response
    return response()->json($data);
});

Route::get('dataBase-Table/{table}',function($table){
    return DB::table($table)->get();
});


Route::get('/send-message', function () {
    broadcast(new NotificationDataRealTime("Hello potetos and tomato After transfer from cdn to file !"));
    return 'Message Sent!';
});

Route::get('list-notification',[NotificationController::class,'listAllNotification'])->name('pusher.listAllNotification');
Route::get('/saml/login', [SamlController::class, 'login'])->name('saml.login');
Route::post('/saml/acs', [SamlController::class, 'acs'])->name('saml.acs');
Route::get('/saml/metadata', [SamlController::class, 'metadata'])->name('saml.metadata');
Route::get('/saml/logout', [SamlController::class, 'logout'])->name('saml.logout');
