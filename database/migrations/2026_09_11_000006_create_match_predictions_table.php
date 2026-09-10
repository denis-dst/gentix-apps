<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('fan_quiz_participants');
        Schema::dropIfExists('fan_quizzes');
        Schema::dropIfExists('match_predictions');

        // Tebak Skor & Prediksi Match
        Schema::create('match_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('tenant_member_id')->constrained('tenant_members')->cascadeOnDelete();
            $table->integer('predicted_home_score');
            $table->integer('predicted_away_score');
            $table->string('predicted_first_goal_scorer')->nullable();
            $table->boolean('is_calculated')->default(false);
            $table->boolean('is_correct')->default(false);
            $table->integer('points_rewarded')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'tenant_member_id']);
            $table->index(['tenant_id', 'event_id']);
        });

        // Kuis & Trivia Suporter
        Schema::create('fan_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            $table->integer('points_reward')->default(50);
            $table->json('questions'); // JSON array of questions, options, & correct answers
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('fan_quiz_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fan_quiz_id')->constrained('fan_quizzes')->cascadeOnDelete();
            $table->foreignId('tenant_member_id')->constrained('tenant_members')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->boolean('is_passed')->default(false);
            $table->integer('points_earned')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['fan_quiz_id', 'tenant_member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fan_quiz_participants');
        Schema::dropIfExists('fan_quizzes');
        Schema::dropIfExists('match_predictions');
    }
};
