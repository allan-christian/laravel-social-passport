<?php

use AllanChristian\SocialPassport\Facades\SocialPassport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $model = app()->make(SocialPassport::getAuthProviderModel());

            $table->bigIncrements('id');
            $table->string('provider_name');
            $table->string('provider_id');

            if ($model->getKeyType() === 'int') {
                $table->unsignedInteger('owner_id');
            } else {
                $table->uuid('owner_id');
            }

            $table->timestamps();

            $table->foreign('owner_id')
                ->references($model->getKeyName())
                ->on($model->getTable())
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_accounts');
    }
}
