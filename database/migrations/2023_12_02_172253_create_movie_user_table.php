<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovieUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_user', function (Blueprint $table) {
            $table->id();
            $table->string('movie_id'); // Внешний ключ на фильмы
            $table->unsignedBigInteger('user_id'); // Внешний ключ на пользователей
            $table->enum('reaction', ['like', 'dislike']); // Поле для реакции пользователя
            $table->unsignedBigInteger('session_id'); // Внешний ключ на сессии
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['movie_id', 'user_id', 'session_id']); // Уникальный ключ для комбинации полей
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_user');
    }
}
