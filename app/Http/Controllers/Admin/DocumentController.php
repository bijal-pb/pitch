<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\TeamUser;
use App\Models\Setting;
use App\Models\Pledge;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use Mail;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $document = UserDocument::query();
    
        if($request->search != null)
        {
            $document = $document->where('first_name','LIKE','%'.$request->search.'%')
                            ->orWhere('last_name','LIKE','%'.$request->search.'%')
                            ->orWhere('email','LIKE','%'.$request->search.'%');
        }
        
        if($request->sortby!= null && $request->sorttype)
        {
            $document = $document->orderBy($request->sortby,$request->sorttype);
        }else{
            $document = $document->orderBy('id','desc');
        }
        if($request->perPage != null){
            $document = $document->paginate($request->perPage);
        }else{
            $document = $document->paginate(10);
        }
       
        if($request->ajax())
        {
            return response()->json( view('admin.document.video_data', compact('u_videos'))->render());
        }
        return view('admin.document.list' , compact(['document']));
    }

    public function documents(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'type',
            2 =>'document',
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $document = UserDocument::where('user_id',$request->user_id);
        
        if($request->type != null){
            $document = $document->where('type',$request->type);
        }

        if($request->search['value'] != null){
            $document = $document->where('type','LIKE','%'.$request->search['value'].'%');
        }
        if($request->length != '-1')
        {
            $document = $document->take($request->length);
        }else{
            $document = $document->take(UserDocument::count());
        }
        $document = $document->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
       
        $data = array();
        if(!empty($document))
        {
            foreach ($document as $u_document)
            {
                $url = route('admin.document.get', ['user_id' => $u_document->id]);
            
                $nestedData['id'] = $u_document->id;
                $nestedData['document_type'] = $u_document->document_type;
                $nestedData['document'] =  "<a href='$u_document->document' target='_blank'>Document</a>";
               
                $data[] = $nestedData;

            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => UserDocument::count(),
            'recordsFiltered' => $request->search['value'] != null ? $document->count() : UserDocument::where('user_id',$request->user_id)->count(),
        ]);
    }
    public function getDocument(Request $request){
        $u_document = UserDocument::find($request->user_id);
        return response()->json(['data'=>$u_document]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }
    
        if($request->user_id != null)
        {
            $u_document = UserDocument::find($request->user_id);
        }else{
            $u_document = new UserDocument;
        }
       
        $u_document->document_type = $request->document_type;
        $u_document->document = $request->document;
        $u_document->save();
        return response()->json(['status'=>'success']);
    }
}