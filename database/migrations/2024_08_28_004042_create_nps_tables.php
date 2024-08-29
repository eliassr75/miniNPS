<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nps', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('range')->default('default');
            $table->string('visibility')->default('default');
            $table->timestamps();
        });

        Schema::create('nps_answers', function (Blueprint $table) {
            $table->foreignId('nps_id')->constrained('nps')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->integer('value')->nullable();
            $table->text('answer')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nps_answers');
        Schema::dropIfExists('nps');
    }
};
