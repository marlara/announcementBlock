<?php

/**
 * @file AnnouncementBlocksSchemaMigration.inc.php
 *
 * Copyright Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class AnnouncementBlocksSchemaMigration
 * @brief Add column for type paths.
 */

namespace APP\plugins\generic\announcementBlocks;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//see also https://github.com/pkp/pkp-lib/blob/a351645bf600be7e1c2ac705c6f6f66ced8e4b3c/classes/announcement/maps/Schema.php

class AnnouncementBlocksSchemaMigration extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('', function (Blueprint $table) {
            $table->string('path'); //see https://github.com/pkp/ops/blob/0659a6a8b2af341cff6bea8099624a9f27451ec6/classes/migration/upgrade/v3_4_0/I7191_SubmissionsDefaultStage.php
        });
    }

    /**
     * Reverse the downgrades
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {
            $table->string('path');
        });
    }
}