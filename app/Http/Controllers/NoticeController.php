<?php
/**
 * User: hefan
 * Date: 17-04-12
 * Time: 15:45
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notice;
use App\User;
use Illuminate\Filesystem\Filesystem;

class NoticeController extends Controller
{
    public function addNotice(Request $request){

        /*
         * 验证管理员权限
         * */

        $filter=$this->filter($request,[
            'title'=>'required|max:255',
            'time'=>'required|date_format:Y-m-d',
            'article'=>'required',
            'writer'=>'required',
        ]);

        if(!$filter)
            return $this->stdResponse('-1');
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        /*添加新闻*/
        try{

            $notice = Notice::create($request->except('api_token'));

            $notice->u_id = $this->user_id;

            $res = $notice->save();

            return $res ? $this->stdResponse('1') : $this->stdResponse('-4');
        }catch (\Exception $exception) {
            return $this->stdResponse('-12');
        }
    }

    //获取新闻内容
    public function getNoticeContent($id){
        /* all visitors allowed*/

        try{
            $notice = Notice::findOrFail($id);
            if(count($notice) == 0){
                return $this->stdResponse('-5');
            }
            return $this->stdResponse('1',$notice);
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
    }

    //获取新闻列表（已经通过）
    public function getNoticeList(Request $request){
        /* request needs to include $page & $rows */
        $notices = Notice::where('state',3)->orderBy('id','desc')
            ->select(['title','time','id'])
            ->paginate($request->rows);


        if(!($request->page >= 1 && $request->page <= $notices->lastPage()))
            return $this->stdResponse('1','{}');

        return $this->stdResponse('1',$notices);

    }

    public function getNoticeListAll(Request $request){
        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        /* request needs to include $page & $rows */
        $notices = Notice::orderBy('id','desc')
            ->select(['title','time','id','writer','state'])
            ->paginate($request->rows);


        if(!($request->page >= 1 && $request->page <= $notices->lastPage()))
            return $this->stdResponse('1','{}');

        return $this->stdResponse('1',$notices);
    }


    public function checkNotice(Request $request,$id){
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
            $item = Notice::find($id);
            if(count($item) == 0){
                return $this->stdResponse('-5');
            }
            $item->state = $request->input('state');
            $res = $item->save();

            return $res ? $this->stdResponse('1') : $this->stdResponse('-14');
        }catch (\Exception $exception){
            return $this->stdResponse('-12');
        }
    }

    public function delNotice(Request $request,$id){
        /* administor api_token checked*/
        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }

        /*删除新闻*/
        try{
            $item = Notice::find($id);
            if(count($item) == 0){
                return $this->stdResponse('-5');
            }
            $res = $item->delete();

            return $res? $this->stdResponse('1') : $this->stdResponse('-4');
        }catch(\Exception $e){
            return $this->stdResponse('-12');
        }
    }

    /*修改新闻内容*/
    public function editNoticeContent(Request $request,$id){

        $filter = $this->filter($request,[
            'title'=>'required|max:255',
            'time'=>'required|date_format:Y-m-d',
            'article'=>'required',
            'writer'=>'required',
            'api_token'=>'required',
        ]);
        if(!$filter)
            return $this->stdResponse('-1');

        if(!$this->check_token($request->input('api_token'))){
            return $this->stdResponse('-3');
        }
        try{
            $new = Notice::find($id);

            $res = $new->update($request->except('api_token'));

            return $res ? $this->stdResponse('1') : $this->stdResponse('-14');

        }catch (\Exception $e){
            return $this->stdResponse('-12');
        }
    }

}
