<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ImportLdapOftUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $roleId;

    /**
     * Create a new job instance.
     *
     * @param array $users
     * @param int $roleId
     */
    public function __construct(array $users, int $roleId)
    {
        $this->users = $users;
        $this->roleId = $roleId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $role = Role::find($this->roleId);

        if (!$role) {
            Log::error("Role not found with ID: {$this->roleId}");
            return;
        }

        foreach ($this->users as $userData) {
            try {
                $this->processUser($userData, $role);
            } catch (\Exception $e) {
                Log::error("Failed to import user {$userData['username']}: " . $e->getMessage());
                continue;
            }
        }
    }

    /**
     * Process individual user
     *
     * @param array $userData
     * @param Role $role
     */
  protected function processUser(array $userData, Role $role)
{
    // Validate required fields
    if (empty($userData['username']) || empty($userData['email'])) {
        Log::warning("User missing required fields (username or email)", ['userData' => $userData]);
        return;
    }

    try {
        $user = User::updateOrCreate(
            ['email' => $userData['email']],
            [
                'username' => $userData['username'], // Make sure to include username if it's a separate field
                'name' => $userData['name'] ?? $userData['username'],
                'phone_number' => $userData['phone'] ?? null,
                'enabled' => 1,
                'password' => Hash::make(12345678),
                'lockout' => 0,
                'type' => 'ldap',
                'role_id' => $role->id,
            ]
        );

        return;
    } catch (\Exception $e) {
        Log::error("Failed to process user {$userData['email']}: " . $e->getMessage());
        return null;
    }
}
    /**
     * Handle job failure
     *
     * @param \Throwable $exception
     */
    public function failed(\Throwable $exception)
    {
        Log::error("ImportLdapOftUsers job failed: " . $exception->getMessage(), [
            'exception' => $exception,
            'role_id' => $this->roleId,
            'user_count' => count($this->users)
        ]);
    }
}
