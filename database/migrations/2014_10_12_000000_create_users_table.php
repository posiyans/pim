<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login')->unique();
            $table->string('name');
            $table->string('full_name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->integer('moderator')->nullable();
            $table->dateTime('last_connect')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('login_by_sms')->default(true);
            $table->integer('sms')->nullable();
            $table->string('color')->nullable();
            $table->integer('hide')->nullable();
            $table->integer('telegram')->nullable();
            $table->json('aliases')->nullable();
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

//        $user = User::create([
//            'name' => 'ps',
//            'login' => 'ps',
//            'full_name' => 'ps',
//            'phone' => '79119612747',
//            'password' => Hash::make('12345')
//        ]);
//        $user->syncRoles(['user', 'admin', 'SuperAdmin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
