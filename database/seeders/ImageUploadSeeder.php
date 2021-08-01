<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ImageUpload;
use Illuminate\Support\Str;

class ImageUploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for($i = 63; $i <= 112; $i++){
            $imageUpload = new ImageUpload();
            $imageUpload->name = Str::random(20);
            $imageUpload->path = "/storage/uploads/" . strval(rand(1, 20)) . ".jpg";
            $imageUpload->is_title_image = 1;
            $imageUpload->article_id = $i;

            $imageUpload->save();
        }
    }
}
