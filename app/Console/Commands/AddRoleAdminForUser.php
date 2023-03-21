<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AddRoleAdminForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add-admin-role {user_login}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавить роль админа пользователю';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $user_login = $this->argument('user_login');
        $user = User::where('login', $user_login)->first();
        if ($user) {
            $user->moderator = true;
            $user->admin = true;
            $user->save();
            echo 'Роль добавленна.';
        } else {
            echo 'Пользователь не найден';
        }
        echo PHP_EOL;
    }


}
