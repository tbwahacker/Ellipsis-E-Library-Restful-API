<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Favourite;
use Illuminate\Support\Facades\Auth;

class FavouritesController extends Controller
{
    public function favourite(Request $request){
        $favourite = Favourite::where('book_id',$request->id)->where('user_id',Auth::user()->id)->get();
        //check if it returns 0 then this book is not liked and should be liked else unliked
        if(count($favourite)>0){
            //bcz we cant have likes more than one
            $favourite[0]->delete();
            return response()->json([
                'success' => true,
                'message' => 'unliked'
            ]);
        }
        $favourite = new Like;
        $favourite->user_id = Auth::user()->id;
        $favourite->book_id = $request->id;
        $favourite->save();

        return response()->json([
            'success' => true,
            'message' => 'liked'
        ]);
    }
}
