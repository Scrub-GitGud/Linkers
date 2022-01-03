<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Link;
use App\Models\LinkFolder;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FolderController extends Controller
{
    public function index(Request $request){
        try {
            $folders = Folder::where('user_id', Auth::user()->id)->get();
            foreach ($folders as $folder) {
                $folder->date = Carbon::parse($folder->created_at)->toFormattedDateString();
            }
            
            return Base::SUCCESS('Succeess', $folders);
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
            $folder = new Folder();
            $folder->user_id = Auth::user()->id;
            $folder->name = $request->name;
            $folder->save();

            $folder->date = Carbon::parse($folder->created_at)->toFormattedDateString();
            
            return Base::SUCCESS('Link Added.', $folder);
        } catch (Exception $e) {
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }


    public function linkFolders(Request $request){
        $validator =  Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());
        try {
            $link = Link::find($request->id);
            $folders = Folder::where('user_id', Auth::user()->id)->get();

            $link_folders = LinkFolder::where('link_id', $link->id)->pluck('folder_id')->toArray();

            foreach ($folders as $folder) {
                $folder['is_added'] = false;
                if(in_array($folder->id, $link_folders))
                    $folder['is_added'] = true;
            }
            
            return Base::SUCCESS('Succeess', $folders);
        } catch (Exception $e) {
            return $e;
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }

    public function folderLinks(Request $request){
        $validator =  Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());
        try {
            $folder = Folder::find($request->id);

            $link_folders = LinkFolder::where('folder_id', $folder->id)->get();
            
            $links = [];
            foreach ($link_folders as $link_folder_i) {
                $link = Link::find($link_folder_i->link_id);
                array_push($links, $link);
            }
            
            return Base::SUCCESS('Succeess', $links);
        } catch (Exception $e) {
            return $e;
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }

    public function addLinkFolders(Request $request){
        $validator =  Validator::make($request->all(), [
            'link_id' => 'required',
            'folder_id' => 'required'
        ]);
        if ($validator->fails()) return Base::ERROR($validator->errors()->first(), $validator->errors());
        try {
            // $link = Link::find($request->link_id);
            // $folder = Folder::find($request->folder_id);
            
            $link_folder_exist = LinkFolder::where([['link_id', $request->link_id], ['folder_id', $request->folder_id]])->first();

            if($link_folder_exist) {
                $link_folder_exist->delete();
                $link_folder = [];
            } else {
                $link_folder = new LinkFolder();
                $link_folder->link_id = $request->link_id;
                $link_folder->folder_id = $request->folder_id;
                $link_folder->save();
            }
            
            return Base::SUCCESS('Succeess', $link_folder);
        } catch (Exception $e) {
            return $e;
            return Base::ERROR('an error occurred!', $e->getMessage());
        }
    }
}
