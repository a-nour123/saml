<?php

use App\Http\Controllers\admin\configure\ConfigureController;
use App\Http\Controllers\admin\configure\AddValuesController;
use App\Http\Controllers\admin\configure\AssetValuesController;
use App\Http\Controllers\admin\configure\AuditLogsController;
use App\Http\Controllers\admin\configure\ImportExportController;
use App\Http\Controllers\admin\configure\RiskCatalogController;
use App\Http\Controllers\admin\configure\ChangePasswordController;
use App\Http\Controllers\admin\configure\RoleManagementController;
use App\Http\Controllers\admin\configure\UserProfileController;
use App\Http\Controllers\admin\configure\UserManagementController;
use App\Http\Controllers\admin\configure\LdapConfigurationController;
use App\Http\Controllers\admin\configure\RiskformulaController;
use App\Http\Controllers\admin\configure\RiskLevelController;
use App\Http\Controllers\admin\configure\ThreatCatalogController;
use App\Http\Controllers\admin\serialnumber\SerialNumberController;
use App\Http\Controllers\admin\configure\AboutController;
use App\Http\Controllers\admin\configure\AssetValueLevelController;
use App\Http\Controllers\admin\configure\ChangeRequestDepartmentController;
use App\Http\Controllers\admin\configure\DomainManagementController;
use App\Http\Controllers\admin\configure\GeneralSettingController;
use App\Http\Controllers\admin\configure\HostRegionController;
use App\Http\Controllers\admin\configure\ServiceDescriptionController;
use App\Http\Controllers\admin\configure\MailSettingsSetupController;
use App\Http\Controllers\admin\configure\QualysAuthSetupController;
use App\Http\Controllers\admin\configure\SeederController;
use App\Http\Controllers\admin\configure\TenableAuthSetupController;
use App\Models\FrameworkControl;
use App\Models\RiskFunction;
use App\Models\RiskGrouping;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\LanguageController;
use App\Http\Controllers\admin\LanguageTranslationController;
use App\Models\HostRegion;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin configure routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

Route::group(
    [
        'prefix' => 'configure', // Prefix applied on all `configure` group routes
        'middleware' => [], // Middlewares applied on all `configure` group routes
        'as' => 'configure.'
    ],
    function () {
        Route::get('/', [ConfigureController::class, 'index'])->name('index');
        Route::post('mail__control_settings', [MailSettingsSetupController::class, 'mailControl'])->name('mailControl.store');
        Route::get('/admin/configure/mailControl/fetch', [MailSettingsSetupController::class, 'fetchMailControl'])->name('mailControl.fetch');
        Route::get('mail_settings', [MailSettingsSetupController::class, 'index'])->name('mail_settings');
        Route::post('mail_settings/store', [MailSettingsSetupController::class, 'store'])->name('mail_settings.store');
        Route::get('tenable_auth', [TenableAuthSetupController::class, 'index'])->name('auth_tenable');
        Route::post('tenable_auth/store', [TenableAuthSetupController::class, 'store'])->name('tenable_auth.store');
        Route::post('tenable_scedule/store', [TenableAuthSetupController::class, 'insertSchedule'])->name('tenable_scedule.store');
        Route::post('sync_tenable/store', [TenableAuthSetupController::class, 'applySync'])->name('applySync.store');
        Route::post('sync_tenable_assets_group/store', [TenableAuthSetupController::class, 'applySyncAssetsGroup'])->name('applySyncAssetsGroup.store');
        Route::post('sync_tenable_assets_region/store', [TenableAuthSetupController::class, 'applySyncAssetsRegion'])->name('applySyncAssetsRegion.store');
        Route::get('tenable/notification_settings', [TenableAuthSetupController::class, 'tenableNotification'])->name('tenableNotification');

        //Qualys
        Route::get('qualys_auth', [QualysAuthSetupController::class, 'index'])->name('auth_qualys');
        Route::post('qualys_auth/store', [QualysAuthSetupController::class, 'store'])->name('qualys_auth.store');
        Route::post('sync_qualys/store', [QualysAuthSetupController::class, 'applySync'])->name('qualysApplySync.store');

        Route::get('add_values', [AddValuesController::class, 'ShowAddValue'])->name('add_values');
        Route::get('userprofile', [UserProfileController::class, 'index'])->name('userprofile.index');
        Route::get('userchangepassword', [UserProfileController::class, 'security'])->name('userprofile.security');
        Route::post('change-password', [ChangePasswordController::class, 'store'])->name('change.password');
        Route::resource('values', AddValuesController::class)->except(['create', 'show', 'edit']);
        Route::resource('asset_values', AssetValuesController::class)->except(['create', 'show', 'edit']);
        Route::resource('risklevel', RiskLevelController::class)->except(['create', 'show', 'edit']);
        Route::resource('assetvaluelevel', AssetValueLevelController::class)->except(['create', 'show', 'edit']);
        Route::resource('hostRegion', HostRegionController::class)->except(['create', 'show', 'edit']);
        Route::get('riskmodels', [RiskformulaController::class, 'showRiskData'])->name('riskmodels.show');
        Route::post('updaterisk', [RiskformulaController::class, 'updateRiskCalculate'])->name('riskmodels.update');
        Route::post('addimpact', [RiskformulaController::class, 'Add_Impact'])->name('impact.create');
        Route::delete('deleteimpact/{id}', [RiskformulaController::class, 'delete_Impactorlikelhood'])->name('impact.delete');
        Route::post('createliklihood', [RiskformulaController::class, 'Add_Likelhood'])->name('likelihood.create');
        Route::get('import', [ImportExportController::class, 'view'])->name('import.index');
        Route::post('file-import', [ImportExportController::class, 'fileImport'])->name('file-import');
        Route::post('file-export', [ImportExportController::class, 'fileExport'])->name('file-export');

        Route::get('riskmodels', [RiskformulaController::class, 'showRiskData'])->name('riskmodels.show');
        Route::post('updaterisk', [RiskformulaController::class, 'updateRiskCalculate'])->name('riskmodels.update');
        Route::post('addimpact', [RiskformulaController::class, 'Add_Impact'])->name('impact.create');
        Route::delete('deleteimpact/{type}', [RiskformulaController::class, 'delete_Impactorlikelhood'])->name('Impactorlikelhood.delete');
        Route::post('createliklihood', [RiskformulaController::class, 'Add_Likelhood'])->name('likelihood.create');

        Route::resource('risk-catalog', RiskCatalogController::class)->except(['create', 'show']);
        Route::resource('threat-catalog', ThreatCatalogController::class);

        Route::get('auditlogs', [AuditLogsController::class, 'index']);
        Route::get('logs-file-export', [AuditLogsController::class, 'downloadExcel'])->name('file-download');

        Route::get('auditlogsdata', [AuditLogsController::class, 'getlogs'])->name('getlogs');
        Route::get('tests', [AuditLogsController::class, 'create']);
        Route::post('teststore', [AuditLogsController::class, 'store'])->name('storename');

        Route::post('updateimpact/{id}', [RiskformulaController::class, 'updateimpact'])->name('updateimpact');
        Route::post('updatelikelhood/{id}', [RiskformulaController::class, 'updatelikelhood'])->name('updatelikelhood');

        // Route::resource('test', TestComplianceController::class);
        // User Management start
        Route::resource('user', UserManagementController::class);
        Route::post('user/ajax/get-users/list', [UserManagementController::class, 'ajaxGetList'])->name('user.ajax.get-users');
        Route::post('user/ajax/export', [UserManagementController::class, 'ajaxExport'])->name('user.ajax.export');
        Route::post('user/check-ldap', [UserManagementController::class, 'CheckUserLdap'])->name('user.check-user-ldap');
        Route::get('user/ajax/account-status/{id}', [UserManagementController::class, 'AccountStatus'])->name('user.ajax.account-status');

        Route::get('import-ldap', [UserManagementController::class, 'openImportLdap'])->name('user.ldapImport');
        Route::post('import-ldap', [UserManagementController::class, 'saveImportLdap'])->name('user.import.ldap.store');
        // User Management end
        Route::group(
            [
                'prefix' => 'logs', // Prefix applied on all `logs` group routes
                'middleware' => [], // Middlewares applied on all `logs` group routes
                'as' => 'logs.'
            ],
            function () {
                Route::get('auditlogs', [AuditLogsController::class, 'index'])->name('index');
            }
        );
        Route::get('auditlogsdata', [AuditLogsController::class, 'getlogs'])->name('getlogs');

        Route::get('tests', [AuditLogsController::class, 'create']);
        Route::post('teststore', [AuditLogsController::class, 'store'])->name('storename');

        Route::group(
            [
                'prefix' => 'roles', // Prefix applied on all `logs` group routes
                'middleware' => [], // Middlewares applied on all `logs` group routes
                'as' => 'roles.'
            ],
            function () {
                Route::get('roles', [RoleManagementController::class, 'index'])->name('index');
                Route::post('roles/store', [RoleManagementController::class, 'store'])->name('role.store');

                Route::group(
                    [
                        'prefix' => 'ajax', // Prefix applied on all `department` group routes
                        'middleware' => [], // Middlewares applied on all `department` group routes
                        'as' => 'ajax.'
                    ],
                    function () {
                        Route::get('edit/{id}', [RoleManagementController::class, 'ajaxGet'])->name('show');
                        Route::post('/{id}', [RoleManagementController::class, 'update'])->name('update');
                        Route::get('delete/{id}', [RoleManagementController::class, 'destroy'])->name('destroy');
                    }
                );
            }
        );

        Route::group(
            [
                'prefix' => 'permissions', // Prefix applied on all `logs` group routes
                'middleware' => [], // Middlewares applied on all `logs` group routes
                'as' => 'permissions.'
            ],
            function () {
                Route::get('/', [RoleManagementController::class, 'permissions'])->name('index');
                Route::get('/all', [RoleManagementController::class, 'Allpermissions'])->name('all');
            }
        );

        // Route::resource('test', TestComplianceController::class);
        Route::group(
            [
                'prefix' => 'extras', // Prefix applied on all `logs` group routes
                'middleware' => [], // Middlewares applied on all `logs` group routes
                'as' => 'extras.'
            ],
            function () {
                Route::get('LDAP-Authentication-Configuration', [LdapConfigurationController::class, 'ConfigurationLDAP'])->name('LDAP-Configuration');
                Route::post('LDAP-Authentication-Configuration', [LdapConfigurationController::class, 'ConfigurationLDAPSave'])->name('LDAP-Configuration.save');
                Route::get('LDAP-test-connection', [LdapConfigurationController::class, 'LDAPTestConnection'])->name('LDAP-test-connection');
            }
        );

        Route::group(
            [
                'prefix' => 'about', // Prefix applied on all `logs` group routes
                'middleware' => [], // Middlewares applied on all `logs` group routes
                'as' => 'about.'
            ],
            function () {
                Route::get('/edit', [AboutController::class, 'edit'])->name('edit');
                Route::group(
                    [
                        'prefix' => 'ajax', // Prefix applied on all `department` group routes
                        'middleware' => [], // Middlewares applied on all `department` group routes
                        'as' => 'ajax.'
                    ],
                    function () {
                        Route::put('/update', [AboutController::class, 'update'])->name('update');
                    }
                );
            }
        );

        Route::group(
            [
                'prefix' => 'general-setting', // Prefix applied on all `logs` group routes
                'middleware' => [], // Middlewares applied on all `logs` group routes
                'as' => 'general_setting.'
            ],
            function () {
                Route::get('/edit', [GeneralSettingController::class, 'edit'])->name('edit');
                Route::group(
                    [
                        'prefix' => 'ajax', // Prefix applied on all `department` group routes
                        'middleware' => [], // Middlewares applied on all `department` group routes
                        'as' => 'ajax.'
                    ],
                    function () {
                        Route::put('/update', [GeneralSettingController::class, 'update'])->name('update');
                    }
                );
            }
        );

        Route::group(
            [
                'prefix' => 'service-description', // Prefix applied on all `logs` group routes
                'middleware' => [], // Middlewares applied on all `logs` group routes
                'as' => 'service_description.'
            ],
            function () {
                Route::get('/edit', [ServiceDescriptionController::class, 'edit'])->name('edit');
                Route::group(
                    [
                        'prefix' => 'ajax', // Prefix applied on all `department` group routes
                        'middleware' => [], // Middlewares applied on all `department` group routes
                        'as' => 'ajax.'
                    ],
                    function () {
                        Route::put('/update', [ServiceDescriptionController::class, 'update'])->name('update');
                    }
                );
            }
        );

        Route::group(
            [
                'prefix' => 'change-request-department', // Prefix applied on all `logs` group routes
                'middleware' => [], // Middlewares applied on all `logs` group routes
                'as' => 'change_request_department.'
            ],
            function () {
                Route::get('/edit', [ChangeRequestDepartmentController::class, 'edit'])->name('edit');
                Route::group(
                    [
                        'prefix' => 'ajax', // Prefix applied on all `department` group routes
                        'middleware' => [], // Middlewares applied on all `department` group routes
                        'as' => 'ajax.'
                    ],
                    function () {
                        Route::put('/update', [ChangeRequestDepartmentController::class, 'update'])->name('update');
                    }
                );
            }
        );

        Route::group([
            'prefix' => 'domain-management', // Prefix applied on all `domain-management` group routes
            'as' => 'domain_management.'
        ], function () {
            Route::get('/', [DomainManagementController::class, 'index'])->name('index');

            Route::group(
                [
                    'prefix' => 'ajax', // Prefix applied on all `department` group routes
                    'middleware' => [], // Middlewares applied on all `department` group routes
                    'as' => 'ajax.'
                ],
                function () {
                    Route::post('/list', [DomainManagementController::class, 'ajaxGetList'])->name('index');
                    Route::post('/', [DomainManagementController::class, 'store'])->name('store');
                    Route::get('edit/{id}', [DomainManagementController::class, 'ajaxGet'])->name('edit');
                    Route::put('/{id}', [DomainManagementController::class, 'update'])->name('update');
                    Route::delete('/{id}', [DomainManagementController::class, 'destroy'])->name('destroy');
                    Route::post('/export', [DomainManagementController::class, 'ajaxExport'])->name('export');
                }
            );
        });

    }

);

   // translation
   Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
   Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
   Route::get('languages/create', [LanguageController::class, 'create'])->name('languages.create');
   Route::get('languages/{language}/translations', [LanguageTranslationController::class, 'index'])->name('languages.translations.index');
   Route::get('languages/{language}/translations/create', [LanguageTranslationController::class, 'create'])->name('languages.translations.create');
   Route::post('languages/{language}/translations', [LanguageTranslationController::class, 'store'])->name('languages.translations.store');
   Route::post('languages/{language?}', [LanguageTranslationController::class, 'update'])->name('languages.translations.update');


