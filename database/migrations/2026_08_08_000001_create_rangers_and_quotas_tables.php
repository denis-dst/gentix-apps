<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rangers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('whatsapp');
            $table->string('bank_name');
            $table->string('account_number');
            $table->enum('gender', ['male', 'female']);
            $table->string('assigned_gate')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ranger_gate_quotas', function (Blueprint $table) {
            $table->id();
            $table->string('gate_name')->unique();
            $table->integer('male_quota')->default(0);
            $table->integer('female_quota')->default(0);
            $table->timestamps();
        });

        // Seed default 10 gate locations: Gate 1-8, VIP, Redemption
        $defaultGates = [
            'Gate 1', 'Gate 2', 'Gate 3', 'Gate 4', 'Gate 5',
            'Gate 6', 'Gate 7', 'Gate 8', 'VIP', 'Redemption'
        ];

        $now = now();
        foreach ($defaultGates as $gate) {
            DB::table('ranger_gate_quotas')->insert([
                'gate_name' => $gate,
                'male_quota' => 0,
                'female_quota' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranger_gate_quotas');
        Schema::dropIfExists('rangers');
    }
};
