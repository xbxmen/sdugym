<?php
/**
 * User: hefan
 * Date: 17-04-12
 * Time: 15:45
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;
use App\Picture;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use App\User;
class NewsController extends Controller
{
	 public function editNews(Request $request){
		/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	} 	 
     	
     	$filter=$this->filter($request,[
     	'title'=>'required|max:255|unique:news',
     	'time'=>'required|date_format:Y-m-d',
     	'article'=>'required',
     	'writer'=>'required|max:12',
     
     	]);	
     	if(!$filter) return $this->stdResponse('-1');
     
     	$news=News::create($request->all());
     	
     	$news->link='/api/news/content/id/'.$news->n_id;
     	
        $admin=User::where('api_token',$request->api_token)->first();
        
     	$news->u_id=$admin->schoolnum;
       	$news->save();
     	if($request->has('image')){
     	//	$astring =$request->image;
		$jsonimg=explode(',',$request->image);
   	    foreach( $jsonimg as $aimage){
	    	Picture::create(['n_id'=>$news->n_id,'path'=>$aimage]);
   	    }
		
     	}
     	return $this->stdResponse('1');
     	
	 } 
	 
	 public function getNewsContent(Request $request,$id){
	 	/* all visitors allowed*/
	 	$news=News::find($id);
	 	return  $this->stdResponse('1',$news);
	 	
	 }
	 
	 public function getNewsList(Request $request){
	 	/* request needs to include $page & $rows */
	 	$allnews=News::where('state',3)->orderBy('n_id','desc')
	 				->paginate($request->rows);;
	 	
		if(!($request->page>=1&&$request->page<=$allnews->lastPage()))  
			return $this->stdResponse('-1');
		else{
			 	$allneeds =collect();
			    foreach($allnews as $new){
	  	        $anews= array('title' =>$new->title ,'time'=>$new->time,'link'=>$new->link ); 
	 	        $allneeds->push($anews);      	
	   			}
	    	return $this->stdResponse('1',$allneeds);
		}
 
	 }
	 
 	 public function getNewsListAll(Request $request){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	} 	 	
     	
	 	/* request needs to include $page & $rows */
	 	$allnews=News::orderBy('n_id','desc')
	 				->paginate($request->rows);;
	 	$allneeds = collect();
	    foreach($allnews as $new){
	          $anews= array('title' =>$new->title ,'time'=>$new->time,'link'=>$new->link, 'id'=>$new->n_id, 'writer'=>$new->writer,'state'=>$new->state);
	          $allneeds->push($anews);      	
	    }
	    return $this->stdResponse('1',$allneeds);
	 
	 }
	 
	 
	 public function checkNews(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$res=$this->filter($request,[
     		'state'=>'required|digits:1|filled']);
     	if(!$res){
     		return $this->stdResponse('-1');
     	}
     	
     	$item=News::find($id);
     	$item->state=$request->input('state');
     	$item->save();
     	return $this->stdResponse('1'); 
	 }
	 
	 public function deNews(Request $request,$id){
    	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	$picpath=Picture::where('n_id',$id)->get();
 //    	return $picpath;
     	foreach($picpath as $item){
     		$npath=$item->path;
     		Storage::disk('pic')->delete($npath);
     	}
     	$item=News::find($id);
     	
     	$item->delete();

     	return $this->stdResponse('1');
	 }
	 
	 public function uploadImg(Request $request){
    	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	$Img=$request->file('newspic');  
        /*get file config*/
	    $originalName = $Img->getClientOriginalName(); 
        $ext = $Img->getClientOriginalExtension();    
        $realPath = $Img->getRealPath();   
        $type = $Img->getClientMimeType();    
        /*upload img*/
        $filename = date('Y-m-d-H-i-s') . '-' .$originalName . '.' . $ext;
        /*use disk pic */
        $bool = Storage::disk('pic')->put($filename, file_get_contents($realPath));

     	return $this->stdResponse('1',$filename);
	 }
	 
	 /*根据picture的id删除图片*/
     public function deImg(Request $request,$id){
    	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}
     	
     	$item=Picture::find($id);
     	
     	$path=$item->path;
     	Storage::disk('pic')->delete($path);
     	
     	$item->delete();
     	return $this->stdResponse('1');
     	
     }
     public function getImg($path){
     	return Storage::disk('pic')->get($path);
     }
}
