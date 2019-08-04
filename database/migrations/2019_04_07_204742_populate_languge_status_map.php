<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Language;
use App\Status;

class PopulateLangugeStatusMap extends Migration
{
    const TABLE_NAME = 'status_map';
    const ROW_VALUE_STATUS = 'active';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $languageCollection = Language::whereNull('deleted_at');
        $languageList = $languageCollection->get();
        $languageItem = $languageCollection->first();
        $languageEntity = Entity::where('name', $languageItem->getTable())->first();
        $status = Status::where('name', self::ROW_VALUE_STATUS)->whereNull('deleted_at')->first();

        foreach ($languageList as $languageItem) {
            DB::table(self::TABLE_NAME)->insert([
                [
                    'entity' => $languageEntity->id,
                    'entity_id' => $languageItem->id,
                    'status_id' => $status->id,
                    'created_by' => 13,
                    'updated_by' => 13,
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $languageCollection = Language::whereNull('deleted_at');
        $languageList = $languageCollection->get();
        $languageItem = $languageCollection->first();
        $languageEntity = Entity::where('name', $languageItem->getTable())->first();

        foreach ($languageList as $languageItem) {
            DB::table(self::TABLE_NAME)->delete([
                [
                    'entity' => $languageEntity->id,
                    'entity_id' => $languageItem->id,
                ],
            ]);
        }
    }
}

