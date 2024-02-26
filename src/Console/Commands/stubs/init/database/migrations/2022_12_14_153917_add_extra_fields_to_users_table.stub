<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->string('phone')->after('email');
            $table->string('model')->after('email_verified_at');
            $table->timestamp('phone_verified_at')->after('email_verified_at')->nullable();
            $table->enum('socialite_signup', [0, 1])->after('password')->default(0);
            $table->enum('form_signup', [0, 1])->after('password')->default(0);
            $table->enum('active', [0, 1])->after('password')->default(1);
            $table->enum('two_factor', [0, 1])->after('password')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->change();
        });

        if (Schema::hasColumn('users', 'two_factor'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('two_factor');
            });
        }

        if (Schema::hasColumn('users', 'socialite_signup'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('socialite_signup');
            });
        }

        if (Schema::hasColumn('users', 'form_signup'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('form_signup');
            });
        }
    }
};
