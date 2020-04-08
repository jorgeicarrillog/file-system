<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB as DB;
use Illuminate\Http\Request;
use App\Folder;
use App\File;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }

    public function generalList(Request $request)
    {
        switch ($request->operation) {
            case 'rename_node':
                return $this->update($request->id,$request->type,$request->except(['id', 'operation']));
                break;
            case 'create_node':
                return $this->store($request->type,$request->except(['operation']));
                break;
            case 'move_node':
                return $this->update($request->id,$request->type,$request->except(['id', 'operation']));
                break;
            case 'copy_node':
                return $this->copy($request->id,$request->type,$request->parent);
                break;
            case 'get_content':
                return $this->show($request->id,$request->type);
                break;
            case 'delete_node':
                return $this->destroy($request->id,$request->type);
                break;
            
            default:
                $files = collect([]);
                if (isset($request->id) && $request->id!='#') {
                    $folders = Folder::select('text','id','parent',DB::raw('TRUE as children'))->where('parent',$request->id)->get();
                    $files = File::select('text','id','parent',DB::raw('FALSE as children'),DB::raw('"file" as type'))->where('parent',$request->id)->get();
                    foreach ($files as $value) {
                        $value['state'] = ['disabled'=>false, 'loaded'=>true];
                    }
                }else{
                    $folders = Folder::select('text','id')->whereNull('parent')->get();
                    foreach ($folders as $value) {
                        if (empty($value['parent'])) {
                            unset($value['parent']);
                        }
                        $value['state'] = ['disabled'=>false, 'loaded'=>false];
                    }
                }

                return $folders->merge($files)->toArray();
                break;
        }
    }

    /**
     * copy the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function copy($id, $type, $parent)
    {
        switch ($type) {
            case 'default':
            case 'folder':
                $folder = Folder::find($id);
                if (!empty($folder)) {
                    $folder_copy = $folder->replicate()->fill([
                        'parent' => $parent
                    ]);
                    if ($folder_copy->save()) {
                        return $folder_copy;
                    }else{
                        return response()->json([],500);
                        
                    }
                }
                break;
            case 'file':
                $file = File::find($id);
                if (!empty($file)) {
                    $file_copy = $file->replicate()->fill([
                        'parent' => $parent
                    ]);
                    if ($file_copy->save()) {
                        return $file_copy;
                    }else{
                        return response()->json([],500);
                        
                    }
                }
                break;
            
            default:
                return [];
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($type, $data)
    {
        switch ($type) {
            case 'default':
            case 'folder':
                return Folder::create(['parent'=>$data['id'],'text'=>$data['text']]);
                break;
            case 'file':
                return File::create(['parent'=>$data['id'],'text'=>$data['text']]);
                break;
            
            default:
                return [];
                break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$type)
    {
        switch ($type) {
            case 'default':
            case 'folder':
                return Folder::select('*',DB::raw('"folder" as type'))->find($id);
                break;
            case 'file':
                return File::select('*',DB::raw('"file" as type'))->find($id);
                break;
            
            default:
                return [];
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id,$type,$data)
    {
        switch ($type) {
            case 'default':
            case 'folder':
                $folder = Folder::find($id);
                if (!empty($folder)) {
                    $folder->fill($data);
                    if ($folder->save()) {
                        return Folder::select('*',DB::raw('"folder" as type'))->find($id);
                    }else{
                        return response()->json([],500);
                        
                    }
                }
                break;
            case 'file':
                $file = File::find($id);
                if (!empty($file)) {
                    $file->fill($data);
                    if ($file->save()) {
                        return File::select('*',DB::raw('"file" as type'))->find($id);
                    }else{
                        return response()->json([],500);
                    }
                }
                break;
            
            default:
                return [];
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $type)
    {
        switch ($type) {
            case 'default':
            case 'folder':
                $folder = Folder::find($id);
                if (!empty($folder)) {
                    return $folder->delete();
                }
                break;
            case 'file':
                $file = File::find($id);
                if (!empty($file)) {
                    return $file->delete();
                }
                break;
            
            default:
                return [];
                break;
        }
    }
}
