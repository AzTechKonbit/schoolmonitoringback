<?php

namespace App\Console\Commands;

use App\Core\Models\User;
use App\Enums\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUserCommand extends Command
{

    protected $signature = '
        user:create
        {--name= : User name}
        {--email= : User email}
        {--password= : User password}
        {--admin : Create as admin}
    ';

    protected $description = 'Create a new user';

    public function handle(): int
    {
        $name = $this->option('name');
        $email = $this->option('email') ;
        $password = $this->option('password') ;
        $isAdmin = $this->option('admin');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email','unique:users,email'],
            'password' => ['required', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'first_name' => $name,
            'last_name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $isAdmin ? UserRole::ADMIN : UserRole::EMPLOYEE,
            'status' => 'active',
            'phone' => '+0000000000000',
        ]);


        $this->components->info(
            "User {$user->email} created successfully."
        );

        return self::SUCCESS;
    }
}
