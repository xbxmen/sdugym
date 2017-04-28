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
    
		if(!($request->page>=1&&$request->page<=$alldoc->lastPage()))  
			return $this->stdResponse('-1');
		else{
	
			return $this->stdResponse('1',$alldoc);
		}
  
  	}
	
	public function uploadDoc(Request $request){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	} 	 
     	//这里验证可以再补充 	
     	$filter=$this->filter($request,[
     	 'document'=>'required',
     	 'document_name'=>'required',
     	]);
     	
    	$doc=$request->file('document');  
        /*get file config*/
	    $originalName = $doc->getClientOriginalName(); 
        $ext = $doc->getClientOriginalExtension();    
        $realPath = $doc->getRealPath();   
        $type = $doc->getClientMimeType();    
        /*upload doc*/
        $filename = date('Y-m-d-H-i-s') . '-' .$originalName ;
        /*use disk doc */
        $bool = Storage::disk('doc')->put($filename, file_get_contents($realPath));
        
        $admin=User::where('api_token',$request->api_token)->first();
        
        $ndoc=new Document;
        $ndoc->path=$filename;
        $ndoc->u_id=$admin->schoolnum;
     	$ndoc->title=$request->document_name;
     	
     	$ndoc->save();
     	
     	return $this->stdResponse('1');    	
	
	}
	
	public function deDoc(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	} 	 
	    $doc=Document::find($id);
	    $docname=$doc->path;
	    $bool=Storage::disk('doc')->delete($docname);
	    if($bool) $doc->delete();
	    
	    return $this->stdResponse('1');
	
	}
	
	public function downDoc(Request $request,$id){
   	    $doc=Document::find($id);
	    $docname=$doc->path;
	    $title=$doc->title;
	    $pathToFile=storage_path('public/documents').'/'.$docname;
	    return response()->download($pathToFile);
	}
}
