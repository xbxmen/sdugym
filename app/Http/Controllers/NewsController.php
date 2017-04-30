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
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;

class NewsController extends Controller
{
	 public function addNews(Request $request){

	     /*
	      * 验证管理员权限
	      * */
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	} 	 
     	
     	$filter=$this->filter($request,[
            'title'=>'required|max:255',
            'time'=>'required|date_format:Y-m-d',
            'article'=>'required',
            'writer'=>'required',
        //    'picture'=>'required'
     	]);
     	if(!$filter)
     	    return $this->stdResponse('-1');

     	/*添加新闻*/
     	try{

     	    $txt = uniqid().'.txt';

            Storage::disk('pic')->put($txt,$request->article);
            $news=News::create($request->except('article'));

            $news->u_id = $this->user_id;
            $news->article = $txt;
            $news->save();
        }catch (\Exception $exception) {
            return $this->stdResponse('-4');
        }
     	return $this->stdResponse('1');
     	
	 } 

     //获取新闻内容
	 public function getNewsContent($id){
	 	/* all visitors allowed*/

	 	$news = News::findOrFail($id);

	 	$news->article = Storage::disk('pic')->read($news->article);

	 	return $this->stdResponse('1',$news);
	 	
	 }

	 //获取新闻列表（已经通过）
	 public function getNewsList(Request $request){
	 	/* request needs to include $page & $rows */
	 	$allnews=News::where('state',3)->orderBy('n_id','desc')
	 				->paginate($request->rows);;
	 	
		if(!($request->page>=1&&$request->page<=$allnews->lastPage()))  
			return $this->stdResponse('-1');
		else{
            $allneeds =collect();
            foreach($allnews as $new){
            $anews= array('title' =>$new->title ,'time'=>$new->time, 'id'=>$new->n_id);
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
	          $anews= array('title' =>$new->title ,'time'=>$new->time, 'id'=>$new->n_id, 'writer'=>$new->writer,'state'=>$new->state);
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
     	try{
            $item=News::find($id);
            $item->state=$request->input('state');
            $item->save();
        }catch (\Exception $exception){
            return $this->stdResponse('-4');
        }
         return $this->stdResponse('1');

     }
	 
	 public function deNews(Request $request,$id){
    	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

        /*删除新闻*/
        try{
            $item=News::find($id);

            /*$path = $item->picture;
            Storage::disk('pic')->delete($path);*/

            $item->delete();
        }catch(\Exception $e){
            return $this->stdResponse('-2');
        }
     	return $this->stdResponse('1');
	 }


	 /*修改新闻内容*/
	 public function editNewsContent(Request $request,$id){

         $filter=$this->filter($request,[
             'title'=>'required|max:255',
             'time'=>'required|date_format:Y-m-d',
             'article'=>'required',
             'writer'=>'required',
         //    'picture'=>'required',
             'api_token'=>'required',
         ]);
         if(!$filter)
             return $this->stdResponse('-1');

         if(!$this->check_token($request->input('api_token'))){
             return $this->stdResponse('-3');
         }
         try{
             $new=News::find($id);

             $new->title = $request->title;

             Storage::disk('pic')->put($new->article, $request->article);
             $new->writer = $request->writer;

             $new->save();
         }catch (\Exception $e){
             return $this->stdResponse('-4');
         }
         return $this->stdResponse('1');
     }


	 //上传图片
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

	 /*给新闻添加图片*/
     public function addNewsPic(Request $request){
         $filter=$this->filter($request,[
             'n_id'=>'required',
             'picture'=>'required|filled',
             'api_token'=>'required',
         ]);
         if(!$filter)
             return $this->stdResponse('-1');

         if(!$this->check_token($request->input('api_token'))){
             return $this->stdResponse('-3');
         }
         /*
          *
          * */
         $res = News::where('n_id','=',$request->input('n_id'))
             ->update(['picture'=>$request->input('picture')]);

         return $this->stdResponse('1',$res);
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
