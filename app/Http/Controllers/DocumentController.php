<?php
/**
 * Created by HBUILDER.
 * User: hefan
 * Date: 17-04-26
 * Time: 12:50
 */
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Document;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
class DocumentController extends Controller{

/***      get documents list       ***/
  
  	public function getList(Request $request){
        /**all visitors allowed **/
    	$alldoc=Document::orderBy('id','desc')->paginate($request->rows);
    
		if(!($request->page>=1 && $request->page<=$alldoc->lastPage()))
			return $this->stdResponse('1');
		else{
	
			return $this->stdResponse('1',$alldoc);
		}
  
  	}
	
	public function uploadDoc(Request $request){

        //这里验证可以再补充
        $filter = $this->filter($request,[
            'document'=>'required|filled',
            'document_name'=>'required|filled',
        ]);
        if(!$filter){
            return $this->stdResponse();
        }
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

        if($this->user_document != 1){
            return $this->stdResponse('-6');
        }

    	$doc=$request->file('document');
        /*get file config*/
        $realPath = $doc->getRealPath();
        /*upload doc*/
        $filename = uniqid().'.'.$doc->getClientOriginalExtension();
        /*use disk doc */
        $bool = Storage::disk('doc')->put($filename, file_get_contents($realPath));

        if(!$bool){
            return $this->stdResponse('-12');
        }
        try{
            $ndoc = new Document();
            $ndoc->path = $filename;
            $ndoc->u_id = $this->user_id;
            $ndoc->title = $request->document_name;

            $ndoc->save();
        }catch (\Exception $exception){
            return  $this->stdResponse('-4');
        }

     	return $this->stdResponse('1');
	
	}
	
	public function deDoc(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
        if($this->user_document != 1){
            return $this->stdResponse('-6');
        }
        try{
            $doc=Document::find($id);
            $docname=$doc->path;
            $bool = Storage::disk('doc')->delete($docname);
            if($bool){
                $doc->delete();
                return $this->stdResponse('1');
            }
                
            return $this->stdResponse('-12');

        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
	}
	
	public function downDoc($id){

	    try{
            $doc = Document::find($id);
            if(count($doc) == 0){
                return $this->stdResponse('-5');
            }

            $name = $doc->path;
            $pathToFile = storage_path('public/documents').'/'.$name;
            return response()->download($pathToFile);
        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
	}
}
