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
        Schema::table('pastas', function (Blueprint $table) {
            $table->uuid('parent_id')->nullable()
                ->after('uuid')
                ->references('uuid')->on('pastas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pastas', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
