<?php
/*
 * ██████╗ ███████╗██╗   ██╗███████╗████████╗ ██████╗ ██████╗ ███╗   ███╗
 * ██╔══██╗██╔════╝██║   ██║██╔════╝╚══██╔══╝██╔═══██╗██╔══██╗████╗ ████║
 * ██║  ██║█████╗  ██║   ██║███████╗   ██║   ██║   ██║██████╔╝██╔████╔██║
 * ██║  ██║██╔══╝  ╚██╗ ██╔╝╚════██║   ██║   ██║   ██║██╔══██╗██║╚██╔╝██║
 * ██████╔╝███████╗ ╚████╔╝ ███████║   ██║   ╚██████╔╝██║  ██║██║ ╚═╝ ██║
 * ╚═════╝ ╚══════╝  ╚═══╝  ╚══════╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝╚═╝     ╚═╝
 * ______________________________________________________________________
 * | Author:    DevStorm Solutions - rplan
 * | Project:   ds-laravel-jwttoken-project
 * | File:      2021_02_05_000000_create_dxtokens_table.php
 * | Created:   05.02.2021
 * | Todo:
 * |_____________________________________________________________________
 */
// 'database/migrations/2021_02_05_000000_create_dxtokens_table.php'

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDxtokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dxtokens', function (Blueprint $table) {
            $table->id();
            $table->string("identified_by");
            $table->string("issued_by");
            $table->dateTime("expires_at");
            $table->string("agent")->nullable();
            $table->string("device")->nullable();
            $table->boolean("is_banned")->default(false);
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dxtokens');
    }
}
