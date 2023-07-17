<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    public function like(Request $request){
        $like = Like::where('book_id',$request->id)->where('user_id',Auth::user()->id)->get();
        //check if it returns 0 then this book is not liked and should be liked else unliked
        if(count($like)>0){
            //bcz we cant have likes more than one
            $like[0]->delete();
            return response()->json([
                'success' => true,
                'message' => 'unliked'
            ]);
        }
        $like = new Like;
        $like->user_id = Auth::user()->id;
        $like->book_id = $request->id;
        $like->save();

        return response()->json([
            'success' => true,
            'message' => 'liked'
        ]);
    }
}
