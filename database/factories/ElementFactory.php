<?php

use Faker\Generator as Faker;
use App\Element;
use App\Constants\ElementType;
use Illuminate\Http\UploadedFile;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Element::class, function (Faker $faker) {
    $types = [ElementType::Text, ElementType::DiwaneeImage, ElementType::Video, ElementType::Heading, ElementType::ElementList];
    
    $type = $types[$faker->numberBetween(0, count($types) - 1)];
    $data = array();
    switch($type) {
        case ElementType::Text:
        case ElementType::Heading:
            $data['text'] = $faker->paragraph;
            $data['format'] = 'html';
            break;
          
        case ElementType::DiwaneeImage:
            $imagesConfig = config('images');
            $data['file']['url'] = $faker->image(base_path() . $imagesConfig['imagesFolder'], 640, 480, null, false);
          
            $data['seoname'] = $faker->text(30);
            $data['seoalt'] = $faker->text(30);
            $data['caption'] = $faker->text(30);
            $data['copyright'] = $faker->text(30);
            break; 
          
        case ElementType::Video:
            $data['remote_id'] = 'FKUAAZSJiGY';
            $data['source'] = 'youtube';
            break; 

        case ElementType::ElementList:
            $count = $faker->numberBetween(2, 5);
            for($index = 0; $index < $count; $index++) {
                $data['listItems'][] = array('content' => $faker->text(20));
            }
            $data['format'] = 'html';
            break;
    }
    
    return [
        'type' => $type,
        'data' => json_encode($data)
    ];
});


$factory->defineAs(Element::class, ElementType::SliderImage, function (Faker $faker) {
    $data = array();
    
    $imagesConfig = config('images');
    $data['file']['url'] = $faker->image(base_path() . $imagesConfig['imagesFolder'], 640, 480, null, false);
    
    $data['alt'] = $faker->text(30);
    $data['seoname'] = $faker->text(30);
    $data['seoalt'] = $faker->text(30);
    $data['caption'] = $faker->text(30);
    $data['copyright'] = $faker->text(30);

    return [
        'type' => ElementType::SliderImage,
        'data' => json_encode($data)
    ];
});