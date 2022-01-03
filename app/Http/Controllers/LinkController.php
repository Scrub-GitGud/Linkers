<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Link;
use App\Models\LinkTag;
use App\Models\Tag;
use App\Models\Vote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    public function getTopLinks(Request $request){
        try {
            $links = Link::where('user_id', Auth::user()->id)->get();

            
            
            return Base::SUCCESS('Succeess', $links);
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }

    public function index(Request $request){
        try {
            $links = Link::where('user_id', Auth::user()->id)->get();

            foreach ($links as $link) {
                $tags = [];
                $ling_tags = LinkTag::where('link_id', $link->id)->get();
                foreach ($ling_tags as $ling_tag_i) {
                    $tag = Tag::find($ling_tag_i->tag_id);
                    array_push($tags, $tag);
                }
                $link['tags'] = $tags;
                $link['url_uuid'] = route('click', $link->uuid);
                $link['clicks'] = Click::where('link_id', $link->id)->count();
                $link['votes'] = Vote::where('link_id', $link->id)->count();
                $link['favicon'] = Vote::where('link_id', $link->id)->count();
            }
            
            return Base::SUCCESS('Succeess', $links);
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }

    public function add(Request $request){
        $validator =  Validator::make($request->all(), [
            'title' => 'required|string',
            'url' => 'required|string',
            'is_private' => 'required',
            'tags' => 'required|array|distinct|min:1',
            "tags.*"  => "required|distinct|min:1",
        ]);
        if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());
        try {
            foreach ($request->tags as $tag_i) {
                $tag_exist = Tag::find($tag_i);
                if(!$tag_exist) return Base::ERROR("Tag not found");
            }
            $user = Auth::user();

            $link = new Link();
            $link->title = $request->title;
            $link->url = $request->url;
            $link->user_id = $user->id;
            $link->uuid = (string) Str::uuid();;
            $link->image_url = $request->image_url;
            $link->is_private = $request->is_private;
            $link->save();

            foreach ($request->tags as $tag_i) {
                $link_tag = new LinkTag();
                $link_tag->tag_id = $tag_i;
                $link_tag->link_id = $link->id;
                $link_tag->save();
            }
            
            return Base::SUCCESS('Link Added.', $link);
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }

    public function update(Request $request){
        $validator =  Validator::make($request->all(), [
            'type' => 'required'
        ]);
        if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());
        try {
            return Base::SUCCESS('Succeess');
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }
    
    public function delete(Request $request){
        $validator =  Validator::make($request->all(), [
            'type' => 'required'
        ]);
        if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());
        try {
            return Base::SUCCESS('Succeess');
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }

    public function toggle(Request $request){
        $validator =  Validator::make($request->all(), [
            'type' => 'required'
        ]);
        if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());
        try {
            return Base::SUCCESS('Succeess');
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }

    public function click($uuid){
        try {
            $ip = request()->ip();

            $link = Link::where('uuid', $uuid)->first();

            $click = new Click();
            $click->user_id = $link->user_id;
            $click->link_id = $link->id;
            $click->ip = $link->ip;
            $click->save();
            
            return redirect($link->url);
        } catch (Exception $e) {
            return Base::ERROR('An error occurred!', $e->getMessage());
        }
    }
    
}
