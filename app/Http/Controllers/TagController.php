<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    public function index(Request $request){
        try {
            $tags = Tag::get(['id', 'name', 'color']);
            return Base::SUCCESS('Succeess', $tags);
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }
    public function add(Request $request){
        $validator =  Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());
        try {
            $tag = new Tag();
            $tag->name = strtolower($request->name);
            $tag->color = $this->rand_color();
            $tag->save();
            
            return Base::SUCCESS('Tag Added.', $tag);
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }
    function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
