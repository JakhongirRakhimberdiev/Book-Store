<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('id');
            $table->string('last_name', 100)->nullable()->after('first_name');
        });

        foreach (DB::table('users')->cursor() as $row) {
            if (! empty($row->first_name)) {
                continue;
            }
            $name = trim((string) $row->name);
            $parts = $name !== '' ? preg_split('/\s+/u', $name, 2) : [];
            $first = $parts[0] ?? 'Foydalanuvchi';
            $last  = $parts[1] ?? '';
            DB::table('users')->where('id', $row->id)->update([
                'first_name' => mb_substr($first, 0, 100),
                'last_name'  => mb_substr($last, 0, 100),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
