<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawdatalistTable extends Migration
{
    public function up()
    {
        Schema::create('rawdatalist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('packet_type');
            $table->string('fleet_number')->nullable();
            $table->string('device_number')->nullable();
            $table->string('date_filter');
            $table->string('filePath')->nullable();
            $table->enum('status', ['incompleted', 'processing', 'completed', 'failed'])->default('incompleted');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('date_filter');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rawdatalist');
    }
}
