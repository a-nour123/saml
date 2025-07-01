<?php

namespace App\Http\Controllers\admin\configure;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Traits\LdapTrait;
use App\Http\Traits\UserPermissionTrait;
use App\Http\Traits\UserTeamTrait;
use App\Http\Traits\UserTrait;
use App\Jobs\ImportLdapOftUsers;
use App\Models\Department;
use App\Models\Job;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\PermissionToUser;
use App\Models\Role;
use App\Models\RoleResponsibility;
use App\Models\Team;
use App\Models\User;
use App\Models\UserToTeam;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\Entry;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    use LdapTrait, UserTrait, UserTeamTrait, UserPermissionTrait;
    private $path = 'admin.content.configure.user_management.';
        public $connection;
    public $container;
    public $CheckConnection = true;
    public $MessageConnection = '';

    /**
     * Display a dump message for testing
     *
     * @return String
     */
    public function create()
    {
        // if (!checkUsersCount(12)) {
        //     return abort(401);
        // }
        $roles = Role::all();
        $teams = Team::all();
        $managers = User::where('manager_id', null)->get();
        $permissions_group = PermissionGroup::all();
        $permissions = Permission::all();
        $jobs = Job::all();
        $departments = Department::all();
        $breadcrumbs = [['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
          ['link' => route('admin.configure.user.index'), 'name' => __('locale.User Management')],
          ['name' => __('locale.AddANewUser')]];
        return view($this->path . 'create', compact('breadcrumbs', 'roles', 'teams', 'managers', 'roles', 'jobs', 'permissions_group', 'departments'));
    }
    /**
     * Display a dump message for testing
     *
     * @return String
     */
    public function store(Request $request)
    {
        if($request->type == "ldap"){
            $rules = array(
                'type' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|unique:users,username',
                'manager_id' => 'nullable|integer',
                'teams' => 'nullable',
                'role_id' => 'required|integer',
                'job_id' => 'nullable|integer',
                'department_id' => 'nullable|integer',
                // 'multi_factor' => 'required',
                // 'permissions' => 'required',
            );
        }else{
            $rules = array(
                'type' => 'required',
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|unique:users,username',
                'manager_id' => 'nullable|integer',
                'password' => 'required|confirmed|min:8',
                'teams' => 'nullable',
                'role_id' => 'required|integer',
                'job_id' => 'nullable|integer',
                'department_id' => 'nullable|integer',
                // 'multi_factor' => 'required',
                // 'permissions' => 'required',
            );
        }


        if (!$request->admin) {
            $request->admin = 0;
        }
        $validator = Validator::make($request->all(), $rules);
        $data = array();

        if ($validator->fails()) {
            $errors = $validator->errors();
            $data = array(
                'status' => 0,
                'errors' => $errors,
            );
        } else {
                // $userCount = User::where('role_id', 1)->count();
                // if ($request->role_id == 1 && $userCount >= 2) {
                //     $data = array(
                //         'status' => 0,
                //         'errors' => ['Cannot add a new user because the maximum limit of 10 users with the role "Administrator" has been reached.'],
                //     );
                // }else{
                    $request->salt = generate_token(20);
                    $user = $this->AddGrcUser($request);
                    $permissions = RoleResponsibility::where('role_id', $request->role_id)->pluck('permission_id')->toArray();

                    if ($request->admin == 1) {
                        $this->AllTeamToUser($user->id);
                        $this->AllPermissionToUser($user->id);
                    } else {
                        $this->AddTeamsOfUser($user->id, $request->teams);
                        $this->AddPermissionsOfUser($user->id, $permissions);
                    }
                    $data = array(
                        'status' => 1,
                        'message' => __('configure.save-information-successfully'),
                    );
                    $teamNames = $user->teams->pluck('name')->toArray();
                    $message = __("locale.ANewUserWithName") . ' "' . ($request->name ?? __('locale.[No Name]')) . '" ' . __('locale.CreatedBy') . ' "' . auth()->user()->name . '".';
                    write_log($user->id, auth()->id(), $message, 'Adding user');
                // }

        }

        return response()->json($data, 200);
    }

    /**
     * Display a dump message for testing
     *
     * @return String
     */
    public function index()
    {
        $breadcrumbs = [['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
        [ 'name' => __('locale.User Management')]];
        $users = User::all();
        $roles = Role::all();
        $departments = Department::all();
        return view($this->path . '.index', compact('roles', 'departments', 'breadcrumbs'));
    }
    /**
     * Display a User edit
     *
     * @return String
     */
    public function edit($id)
    {
        if ($id == 1 && auth()->id() != 1) {
            return abort(401);
        }

        $roles = Role::all();
        $teams = Team::all();
        $managers = User::where('manager_id', null)->get();
        $permissions_group = PermissionGroup::all();
        $permissions = Permission::all();
        $jobs = Job::all();
        $breadcrumbs = [['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
          ['link' => route('admin.configure.user.index'),'name' => __('locale.User Management')],
          ['name' => __('locale.UpdateUser')]];
        $editUser = User::findOrFail($id);
        $editUserTeam = $this->GetTeamsOfUser($id);
        $departments = Department::all();

        return view($this->path . 'edit', compact('breadcrumbs', 'roles', 'teams', 'managers', 'roles', 'jobs', 'permissions_group', 'editUser', 'editUserTeam', 'departments'));
    }

    /**
     * Return a listing of the resource after some manipulation.
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetList(Request $request)
    {
        /* Start reading datatable data and custom fields for filtering */
        $dataTableDetails = [];
        $customFilterFields = [
            'normal' => ['type', 'username'],
            'relationships' => ['role', 'department'],
            'other_global_filters' => ['name', 'email'],
        ];
        $relationshipsWithColumns = [
            // 'relationshipName:column1,column2,....'
            'role:id,name',
            'department:id,name',
        ];

        prepareDatatableRequestFields($request, $dataTableDetails, $customFilterFields);
        /* End reading datatable data and custom fields for filtering */

        // Getting total records count with and without apply global search
        [$totalRecords, $totalRecordswithFilter] = getDatatableFilterTotalRecordsCount(
            User::class,
            $dataTableDetails,
            $customFilterFields
        );

        $mainTableColumns = getTableColumnsSelect(
            'users',
            [
                'id',
                'type',
                'username',
                'name',
                'email',
                'admin',
                'enabled',
                'role_id',
                'department_id'
            ]
        );

        // Getting records with apply global search */
        $users = getDatatableFilterRecords(
            User::class,
            $dataTableDetails,
            $customFilterFields,
            $relationshipsWithColumns,
            $mainTableColumns
        );

        // Custom users response data as needs
        $data_arr = [];
        foreach ($users as $user) {
            $data_arr[] = array(
                'id' => $user->id,
                'type' => $user->type,
                'username' => showBOLB($user->username),
                'name' => $user->name,
                'email' => showBOLB($user->email),
                'role' => $user->role->name,
                'admin' => $user->admin,
                'active' => $user->enabled,
                // 'ldap_department' => $user->department,
                'department' => ($user->department) ? $user->department->name : '-',
                'Actions' => $user->id,
            );
        }

        // Get custom response for datatable ajax request
        $response = getDatatableAjaxResponse(intval($dataTableDetails['draw']), $totalRecords, $totalRecordswithFilter, $data_arr);

        return response()->json($response, 200);


        ######################################
        $Users = User::get()->map(function ($user) {
            return (object) [
                'responsive_id' => $user->id,
                'type' => $user->type,
                'username' => showBOLB($user->username),
                'name' => $user->name,
                'email' => showBOLB($user->email),
                'role_id' => $user->role->name,
                'admin' => $user->admin,
                'active' => $user->enabled,
                'ldap_department' => $user->department,
                'department_id' => ($user->department) ? $user->department->name : '-',
                'Actions' => $user->id,
            ];
        });

        return response()->json($Users, 200);
    }
    /**
     * check if user found in ldap
     *
     * @return String
     */
    public function CheckUserLdap(Request $request)
    {
        $rules = array(
            // 'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',

        );

        $validator = Validator::make($request->all(), $rules);
        $data = array();
        if ($validator->fails()) {
            $errors = $validator->errors();
            $data = array(
                'status' => 0,
                'errors' => $errors,
            );
        } else {
            $check = $this->CheckExistUserLdap($request->email, $request->username);
            if ($check) {
                $data = array(
                    'status' => true,
                    'check' => true,
                    'user' => $check,
                );
            } else {
                $data = array(
                    'status' => true,
                    'check' => false,
                    'message' => __('configure.UserNotFoundInLdap'),
                );
            }
        }
        return response()->json($data, 200);
    }

    /**
     * delete user by id
     *
     * @return String
     */
    // public function destroy($id)
    // {
    //     $this->RemoveUserTeam($id);
    //     $this->RemoveUserPermission($id);
    //     $user = User::where('id', $id)->delete();
    //     $message = __('locale.User') . ' "' . $user->name . '" ' . __('locale.DeletedBy') . ' "' . auth()->user()->name . '".';
    //     write_log($user->id, auth()->id(), $message, 'deleting User');
    //     return response()->json('ok', 200);
    // }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if (
                $user->department_id == null &&
                $user->manager_id == null &&
                $user->job_id == null &&
                UserToTeam::where('user_id', $user->id)->first() == null
            ) {
                PermissionToUser::where('user_id', $user->id)->delete();
                $user->delete();

                $message = __('locale.User') . ' "' . $user->name . '" ' . __('locale.DeletedBy') . ' "' . auth()->user()->name . '".';
                write_log($user->id, auth()->id(), $message, 'deleting User');

                return response()->json(['message' => __('configure.EmployeeDeletedSuccessfully')], 200);
            } else {
                throw new \Exception(__('configure.UserCannotDeleteBecauseRelatedWithDepartmentOrTeam'));
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * change account status user by id
     *
     * @return String
     */
    public function AccountStatus($id)
    {
        if ($id == 1)
            return response()->json('ok', 200);

        $user = User::find($id);
        if ($user->enabled == 1) {
            $user->enabled = 0;
            $user->save();
        } else {

            $user->enabled = 1;
            $user->save();
        }
        return response()->json('ok', 200);
    }

    /**
     * edit user
     *
     * @return String
     */
    public function update(Request $request)
    {
        if ($request->id == 1 && auth()->id() != 1) {
            $response = array(
                'status' => false,
                'message' => __('locale.YouDonotHavePermissionToDoThat'),
            );
            return response()->json($response, 401);
        }
        $rules = array(
            'name' => 'required',
            'manager_id' => 'nullable|integer',
            'password' => 'nullable|confirmed|min:8',
            'teams' => 'nullable',
            'role_id' => 'required|integer',
            'job_id' => 'nullable|integer',
            'department_id' => 'nullable|exists:departments,id',
        );
        if (!$request->admin) {
            $request->admin = 0;
        }
        $validator = Validator::make($request->all(), $rules);
        $data = array();

        if ($validator->fails()) {
            $errors = $validator->errors();
            $data = array(
                'status' => 0,
                'errors' => $errors,
            );
        } else {
            $userCount = User::where('role_id', 1)->count();
            if ($request->role_id == 1 && $userCount >= 2) {
                $data = array(
                    'status' => 0,
                    'errors' => ['Cannot add a new user because the maximum limit of 10 users with the role "Administrator" has been reached.'],
                );
            }else{
                $user = $this->EditGrcUser($request);
                $permissions = RoleResponsibility::where('role_id', $request->role_id)->pluck('permission_id')->toArray();
                $teams = $request->teams;
                if ($request->admin == 1) {
                    $teams = Team::pluck('id')->toArray();
                    $permissions = Permission::pluck('id')->toArray();
                }
                $this->UpdateTeamsOfUser($request->id, $teams);
                $this->UpdatePermissionsOfUser($request->id, $permissions);

                $data = array(
                    'status' => 1,
                    'message' => __('configure.save-information-successfully'),
                );
                $message = __('locale.User') . ' "' . $user->name . '" ' . __('locale.UpdatedBy') . ' "' . auth()->user()->name . '".';
                write_log($user->id, auth()->id(), $message, 'Updating User');


            }
        }


        return response()->json($data, 200);
    }
    /**
     * Return an Export file for listing of the resource after some manipulation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxExport(Request $request)
    {
        if ($request->type != 'pdf')
            return Excel::download(new UsersExport, 'Users.xlsx');
        else
            return 'Users.pdf';
    }



public function openImportLdap()
{
    $breadcrumbs = [
        ['link' => route('admin.dashboard'), 'name' => __('locale.Dashboard')],
        ['link' => 'javascript:void(0)', 'name' => __('locale.Configure')],
        ['link' => route('admin.configure.user.index'), 'name' => __('locale.Users')],
        ['name' => __('user.ldap_import')]
    ];

    $roles = Role::all();

    try {
        $this->LdapConnection();

        $excludedOUs = ['Domain Controllers', 'Microsoft Exchange Security Groups'];

        $tree = [];

        // Get all OUs first
        $ous = $this->connection->query()
            ->where('objectClass', '=', 'organizationalUnit')
            ->get();

        // Get all groups separately
        $groups = $this->connection->query()
            ->where('objectClass', '=', 'group')
            ->get();

        // Build OU structure
        foreach ($ous as $ou) {
            $dn = $ou['dn'];

            // Extract OU path from DN
            preg_match_all('/OU=([^,]+)/', $dn, $matches);

            if (!empty($matches[1])) {
                $hierarchicalOUs = array_reverse($matches[1]);

                // Filter out excluded OUs
                $hierarchicalOUs = array_filter($hierarchicalOUs, function ($ouName) use ($excludedOUs) {
                    return !in_array($ouName, $excludedOUs);
                });

                if (!empty($hierarchicalOUs)) {
                    $currentNode = &$tree;

                    foreach ($hierarchicalOUs as $ouName) {
                        if (!isset($currentNode[$ouName])) {
                            $currentNode[$ouName] = [];
                        }
                        $currentNode = &$currentNode[$ouName];
                    }
                }
            }
        }

        // Assign groups to their OUs
        foreach ($groups as $group) {
            $dn = $group['dn'];
            $groupName = $group['cn'][0] ?? '(Unnamed Group)';

            preg_match_all('/OU=([^,]+)/', $dn, $matches);
            if (!empty($matches[1])) {
                $hierarchicalOUs = array_reverse($matches[1]);
                $hierarchicalOUs = array_filter($hierarchicalOUs, function ($ouName) use ($excludedOUs) {
                    return !in_array($ouName, $excludedOUs);
                });

                if (!empty($hierarchicalOUs)) {
                    $currentNode = &$tree;
                    $found = true;

                    // Navigate to the deepest OU
                    foreach ($hierarchicalOUs as $ouName) {
                        if (!isset($currentNode[$ouName])) {
                            $found = false;
                            break;
                        }
                        $currentNode = &$currentNode[$ouName];
                    }

                    if ($found) {
                        if (!isset($currentNode['groups'])) {
                            $currentNode['groups'] = [];
                        }
                        $currentNode['groups'][] = [
                            'count' => 1,
                            0 => $groupName
                        ];
                    }
                }
            }
        }

    } catch (\Throwable $th) {
        $ldapMessage = 'Can\'t contact LDAP server: ' . $th->getMessage();
        return view('admin.content.configure.user_management.ldap_import', compact('breadcrumbs', 'ldapMessage', 'roles'));
    }

    return view('admin.content.configure.user_management.ldap_import', compact('breadcrumbs', 'tree', 'roles'));
}




    // public function saveImportLdap(Request $request)
    // {
    //     // dd($request->all());

    //     try {

    //         $lastDepartment = $request->group_name;
    //         $ou =  $request->group_ou_path;

    //         $users = $this->getAllUsersUnderGroup($ou,$lastDepartment);
    //         dd($users);


    //         return response()->json([
    //             'success' => true,
    //             'message' => __('locale.DepartmentsImportedSuccessfully'),
    //             'redirect' => route('admin.hierarchy.department.index')
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function saveImportLdap(Request $request)
{
    try {
        $lastDepartment = $request->group_name;
        $ou = $request->group_ou_path;
        $roleId = $request->role_id;

        // Get all users from LDAP group
        $users = $this->getAllUsersUnderGroup($ou, $lastDepartment);

        if (empty($users)) {
            return response()->json([
                'success' => false,
                'message' => __('locale.NoUsersFoundInGroup')
            ], 400);
        }


        // Process users in chunks of 500
        $userChunks = array_chunk($users, 200);


        foreach ($userChunks as $chunk) {
            // Prepare the data for the job
            $userData = array_map(function($user) {
                $username = $user['samaccountname'][0] ??
                           strtolower(str_replace(' ', '.', $user['cn'][0] ?? ''));

                return [
                    'name' => $user['cn'][0] ?? '',
                    'username' => $username,
                    'email' => $user['mail'][0] ?? '',
                    'phone' => $user['telephonenumber'][0] ?? '',
                    'department' => $user['department'][0] ?? '',
                    'title' => $user['title'][0] ?? '',
                    // Add any other fields you need
                ];
            }, $chunk);


            dispatch(new ImportLdapOftUsers($userData, $roleId))
                    ->delay(now()->addSeconds(10));
        }

        return response()->json([
            'success' => true,
            'message' => __('locale.UsersImportStarted'),
            'redirect' => route('admin.configure.user.index')
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    // public function getAllUsersUnderOU($ouName)
    // {
    //     $baseDn = getLdapValue('LDAP_DEFAULT_BASE_DN');

    //     $this->LdapConnection();
    //     // بحث: جيب كل OUs اللي اسمها يساوي $ouName عشان تجيب DN الدقيق
    //     $ouEntry = $this->connection->query()
    //         ->where('objectClass', '=', 'organizationalUnit')
    //         ->where('ou', '=', $ouName)
    //         ->first();

    //     if (!$ouEntry) {
    //         return [];
    //     }

    //     $ouDn = $ouEntry['dn'];

    //     // Search for users under this OU and all sub-OUs
    //     // $users = $this->connection->query()
    //     //     ->in($ouDn)  // Set base search DN
    //     //     ->where('objectClass', '=', 'user')
    //     //     ->get();

    //         $users = $this->connection->query()
    //         ->in($ouDn)
    //         ->where('objectClass', '=', 'user')
    //         ->first();

    //     return $users;
    // }

    public function getAllUsersUnderGroup($ouName, $groupName)
    {
        $baseDn = getLdapValue('LDAP_DEFAULT_BASE_DN');

        $this->LdapConnection();


        $ouEntry = $this->connection->query()
            ->where('objectClass', '=', 'organizationalUnit')
            ->where('ou', '=', $ouName)
            ->first();

        if (!$ouEntry) {
            return [];  // OU غير موجود
        }

        $ouDn = $ouEntry['dn'];

        $groupEntry = $this->connection->query()
            ->in($ouDn)
            ->where('objectClass', '=', 'group')
            ->where('cn', '=', $groupName)
            ->first();

        if (!$groupEntry || !isset($groupEntry['member'])) {
            return [];
        }

        $membersDn = (array) $groupEntry['member'];

        $users = [];


        foreach ($membersDn as $memberDn) {
            $user = $this->connection->query()
                ->where('distinguishedName', '=', $memberDn)
                ->first();

            if ($user) {
                $users[] = $user;
            }
        }

        return $users;
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
            // 'username' =>  getLdapValue('LDAP_DEFAULT_USERNAME'),
            'username' => getLdapValue('LDAP_DEFAULT_USERNAME'),
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
        }
    }


}
