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

     	$filter=$this->filter($request,[
            'title'=>'required|max:255',
            'time'=>'required|date_format:Y-m-d',
            'article'=>'required|filled',
            'writer'=>'required|filled',
         //   'picture'=>'required|filled'
     	]);
         /*
        * 验证管理员权限
        * */
         if(!$this->check_token($request->input('api_token'))){
             return $this->stdResponse('-3');
         }

         if(!$filter)
     	    return $this->stdResponse('-1');

     	/*用户需要有管理新闻的权限 */
     	if($this->user_news != 1 ){
            return $this->stdResponse('-6');
        }
     	/*添加新闻*/
     	try{
     	    $txt = uniqid().'.txt';

            Storage::disk('pic')->put($txt,$request->article);

            $news = new News();

            $news->u_id = $this->user_id;
            $news->article = $txt;
            $news->picture = $request->picture ? $request->picture : "";
            $news->writer = $request->writer ? $request->writer : "";
            $news->time = $request->time ? $request->time : "";
            $news->title = $request->title ? $request->title : "";

            $res = $news->save();
            return $res ? $this->stdResponse('1') : $this->stdResponse('-4');
        }catch (\Exception $exception) {
            return $this->stdResponse('-12');
        }catch (\Error $error){
            return $this->stdResponse('-12');
        }
	 }

     //获取新闻内容
	 public function getNewsContent($id){
	 	/* all visitors allowed*/

	 	try{
            $news = News::findOrFail($id);

            if(count($news) == 0){
                return $this->stdResponse('-5');
            }
            $news->article = Storage::disk('pic')->read($news->article);

            return $this->stdResponse('1',$news);

        }catch (\Error $exception){
            return $this->stdResponse('-12');
        }
	 }

	 //获取新闻列表（已经通过）
	 public function getNewsList(Request $request){
	 	/* request needs to include $page & $rows */
	 	$news = News::where('state',3)->orderBy('n_id','desc')
             ->select(['title','time','n_id as id','picture'])
             ->paginate($request->rows);

         if(!($request->page >=1 && $request->page <= $news->lastPage()))
             return $this->stdResponse('1','{}');
         else{
             return $this->stdResponse('1',$news);
         }
 
	 }
	 
 	 public function getNewsListAll(Request $request){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

         /*用户需要有管理新闻的权限 */
         if($this->user_news != 1 ){
             return $this->stdResponse('-6');
         }

         /* request needs to include $page & $rows */
         $news = News::orderBy('n_id','desc')
             ->select(['title','time','n_id as id','state','writer','picture'])
             ->paginate($request->rows);

         if(!($request->page >=1 && $request->page <= $news->lastPage()))
             return $this->stdResponse('1','{}');
         else{
             return $this->stdResponse('1',$news);
         }

	 }
	 
	 
	 public function checkNews(Request $request,$id){
     	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

         /*用户需要有管理新闻的权限 */
         if($this->user_news != 1 ){
             return $this->stdResponse('-6');
         }
     	
     	$res = $this->filter($request,[
     		'state'=>'required|max:2|filled']);
     	if(!$res){
     		return $this->stdResponse('-1');
     	}
     	try{
            $item = News::find($id);
            if(count($item) == 0){
                return $this->stdResponse('-5');
            }
            $item->state=$request->input('state');
            $res = $item->save();

            return $res ? $this->stdResponse('1') : $this->stdResponse('-14');
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
     }
	 
	 public function delNews(Request $request,$id){
    	/* administor api_token checked*/
     	if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	}

         /*用户需要有管理新闻的权限 */
         if($this->user_news != 1 ){
             return $this->stdResponse('-6');
         }

        /*删除新闻*/
        try{
            $item = News::find($id);
            if(count($item) == 0){
                return $this->stdResponse('-5');
            }

            $path = $item->article;
            $res01 = Storage::disk('pic')->delete($path);

            /*$path = $item->picture;
            Storage::disk('pic')->delete($path);*/

            $res02 = $item->delete();

            return ($res01 && $res02)? $this->stdResponse('1') : $this->stdResponse('-4');

        }catch(\Exception $e){
            return $this->stdResponse('-12');
        }
	 }


	 /*修改新闻内容*/
	 public function editNewsContent(Request $request,$id){

         $filter=$this->filter($request,[
             'title'=>'required|max:255',
             'time'=>'required|date_format:Y-m-d',
             'article'=>'required|filled',
             'writer'=>'required|filled',
   //          'picture'=>'required|filled',
             'api_token'=>'required|filled',
         ]);
         if(!$filter)
             return $this->stdResponse('-1');

         if(!$this->check_token($request->input('api_token'))){
             return $this->stdResponse('-3');
         }

         /*用户需要有管理新闻的权限 */
         if($this->user_news != 1 ){
             return $this->stdResponse('-6');
         }
         try{
             $new = News::find($id);

             $new->title = $request->input('title');
             $new->writer = $request->input('writer');
             $new->time = $request->input('time');
             $new->picture = $request->picture ? $request->picture : "";

             $res01 = Storage::disk('pic')->put($new->article, $request->article);

             $res02 = $new->save();

             return ($res01 && $res02)? $this->stdResponse('1') : $this->stdResponse('-4');

         }catch (\Exception $e){
             return $this->stdResponse('-12');
         }
     }

	 //上传图片
	 public function uploadImg(Request $request){
    	 /* administor api_token checked*/
     	 if(!$this->check_token($request->input('api_token'))){
     		 return $this->stdResponse('-3');
         }
         /*用户需要有管理新闻的权限 */
         if($this->user_news != 1 ){
             return $this->stdResponse('-6');
         }

         $filter=$this->filter($request,[
            // 'newspicture' => 'dimensions:min_width=100,min_height=200',
             'api_token'=>'required|filled',
         ]);
         if(!$filter)
             return $this->stdResponse('-1');

     	$Img = $request->file('newspicture');

        /*get file config*/
        $ext = $Img->getClientOriginalExtension();
        $realPath = $Img->getRealPath();

        /*upload img*/
        $filename = uniqid(). '.' . $ext;

        try{
            /*use disk pic */
            $bool = Storage::disk('pic')->put($filename, file_get_contents($realPath));

            return $bool? $this->stdResponse('1',$filename) : $this->stdResponse('-12');

        }catch (\Error $error){
            return $this->stdResponse('-12');
        }

	 }

     /*显示 图片*/
     public function getImg($path){
        return Storage::disk('pic')->get($path);
     }






	 /*给新闻添加图片*/
     public function addNewsPic(Request $request){
         $filter = $this->filter($request,[
             'n_id'=>'required',
             'picture'=>'required|filled',
             'api_token'=>'required',
         ]);
         if(!$filter)
             return $this->stdResponse('-1');

         if(!$this->check_token($request->input('api_token'))){
             return $this->stdResponse('-3');
         }

         /*用户需要有管理新闻的权限 */
         if($this->user_news != 1 ){
             return $this->stdResponse('-6');
         }
         /*
          *
          * */
         $res = News::where('n_id','=',$request->input('n_id'))
             ->update(['picture'=>$request->input('picture')]);

         return $this->stdResponse('1',$res);
     }

	 /*根据picture的id删除图片*/
     public function delImg(Request $request,$id){
    	 /* administor api_token checked*/
     	 if(!$this->check_token($request->input('api_token'))){
     		return $this->stdResponse('-3');
     	 }
     	
     	 $item = Picture::find($id);
     	
     	 $path = $item->path;
     	 $res01 = Storage::disk('pic')->delete($path);
     	
     	 $res02 = $item->delete();
     	 return ($res01 && $res02)? $this->stdResponse('1') : $this->stdResponse('-14');

     }


}
