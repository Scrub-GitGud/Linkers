<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // $contents = file_get_contents("https://www.youtube.com/watch?v=AKnpqlVJ0Wg");
    // return $snapshot = json_decode($contents, true);
    $url = "https://www.youtube.com";
    $screen_shot_json_data = file_get_contents("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url&key=AIzaSyAwsny28DC0c1hdOVgUSnsKz95TUKKH9XM");
    $screen_shot_result = json_decode($screen_shot_json_data, true);
    $screen_shot = $screen_shot_result['screenshot']['data'];
    $screen_shot = str_replace(array('_','-'), array('/', '+'), $screen_shot);
    $screen_shot_image = "<img src=\"data:image/jpeg;base64,".$screen_shot."\" class='img-responsive img-thumbnail'/>";
    
    return view('home');
});

Route::get('click/{uuid}', [LinkController::class, 'click'])->name('click');