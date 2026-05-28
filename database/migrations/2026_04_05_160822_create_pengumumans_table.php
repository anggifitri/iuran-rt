<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengumumans', function (Blueprint $table) {  // ← pengumumans
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengumumans');
    }
};
