<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BooksController extends Controller
{
    use ApiHelpers;



    /////////////////// HERE /////////////////////

//
//
//    public function update(Request $request){
//        $post = Post::find($request->id);
//        // check if user is editing his own post
//        // we need to check user id with post user id
//        if(Auth::user()->id != $post->user_id){
//            return response()->json([
//                'success' => false,
//                'message' => 'unauthorized access'
//            ]);
//        }
//        $post->desc = $request->desc;
//        $post->update();
//        return response()->json([
//            'success' => true,
//            'message' => 'post edited'
//        ]);
//    }
//
//    public function delete(Request $request){
//        $post = Post::find($request->id);
//        // check if user is editing his own post
//        if(Auth::user()->id !=$post->user_id){
//            return response()->json([
//                'success' => false,
//                'message' => 'unauthorized access'
//            ]);
//        }
//
//        //check if post has photo to delete
//        if($post->photo != ''){
//            Storage::delete('public/posts/'.$post->photo);
//        }
//        $post->delete();
//        return response()->json([
//            'success' => true,
//            'message' => 'post deleted'
//        ]);
//    }
//
//    public function posts(){
    //    $posts = Post::orderBy('id','desc')->get();
    //    foreach($posts as $post){
    //        //get user of post
    //        $post->user;
    //        //comments count
    //        $post['commentsCount'] = count($post->comments);
    //        //likes count
    //        $post['likesCount'] = count($post->likes);
    //        //check if users liked his own post
    //        $post['selfLike'] = false;
    //        foreach($post->likes as $like){
    //            if($like->user_id == Auth::user()->id){
    //                $post['selfLike'] = true;
    //            }
    //        }

    //    }

    //    return response()->json([
    //        'success' => true,
    //        'posts' => $posts
    //    ]);
//    }

    ////////////////// HERE /////////////////////







    public function book(Request $request): \Illuminate\Http\JsonResponse
    {

            // $post = DB::table('books')->get();
            // return $this->onSuccess($post, 'Book Retrieved');


            $perPage = $request->input('page', 10);
            if (!is_numeric($perPage)) {
              $perPage = 10;
            }

            // $books = Book::withCount('likes')->orderBy('likes_count', 'desc')->paginate($perPage);
            $books = Book::withCount('likes')->orderBy('likes_count', 'desc')->get();
            foreach($books as $book){
                //get user of bo
                $book->user;
                //comments count
                $book['commentsCount'] = count($book->comments);
                //likes count
                $book['likesCount'] = count($book->likes);
             
                //check if users liked his own post
                $book['selfLike'] = false;
                foreach($book->likes as $like){
                if($like->user_id == Auth::user()->id){
                    $book['selfLike'] = true;
                }
            }                
            }

        
            return response()->json([
                'status' => 200,
                'message' => "Retrieved",
                'data' => $books
            ]);



    }



    public function createBook(Request $request): \Illuminate\Http\JsonResponse
    {

       $user = $request->user();
       if ($this->isAdmin($user)){
           $validator = Validator::make($request->all(), $this->postValidationRules());
           if ($validator->passes()){
               // Create New Book
               $book = new Book();
               $book->user_id = Auth::user()->id;
               $book->title = $request->input('title');
               $book->slug = Str::slug($request->input('title'));
               $book->content = $request->input('content');
               $book->save();
               $book->user;

               return $this->onSuccess($book, 'Book Created');
           }
           return $this->onError(400, $validator->errors());
       }

        return $this->onError(401, 'Unauthorized Access');

    }

    public function updateBook(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if ($this->isAdmin($user)){
            $validator = Validator::make($request->all(), $this->postValidationRules());
            if ($validator->passes()){
                // Create New Book
                $book = Book::find($request->id);
                $book->title = $request->input('title');
                $book->content = $request->input('content');
                $book->update();

                return $this->onSuccess($book, 'Book Updated');
            }
            return $this->onError(400, $validator->errors());
        }

        return $this->onError(401, 'Unauthorized Access');
    }

    public function deleteBook(Request $request): \Illuminate\Http\JsonResponse
    {
        
        $user = $request->user();
        // if ($this->isAdmin($user) || $this->isUser($user)){
            $book = Book::find($request->id); // Find the id of the post passed
            $book->delete();         // Delete the specific post data
            if (!empty($book)){
                return $this->onSuccess($book, 'Book Deleted');
            }
            return $this->onError(404, 'Book Not Found');
        // }
        return $this->onError(401, 'Unauthorized Access');
    }

    public function createUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if ($this->isAdmin($user)){
            $validator = Validator::make($request->all(), $this->userValidatedRules());
            if ($validator->passes()){
                // Create New Writer
                User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => 2,
                    'password' => Hash::make($request->input('password'))
                ]);

                $writerToken =  $user->createToken('auth_token', ['writer'])->plainTextToken;
                return $this->onSuccess($writerToken, 'User Created With Writer Privilege');
            }
            return $this->onError(400, $validator->errors());
        }

        return $this->onError(401, 'Unauthorized Access');

    }

    public function createSubscriber(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if ($this->isAdmin($user)){
            $validator = Validator::make($request->all(), $this->userValidatedRules());
            if ($validator->passes()){
                // Create New Writer
                User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => 3,
                    'password' => Hash::make($request->input('password'))
                ]);

                $writerToken =  $user->createToken('auth_token', ['subscriber'])->plainTextToken;
                return $this->onSuccess($writerToken, 'User Created With Subscriber Privilege');
            }
            return $this->onError(400, $validator->errors());
        }

        return $this->onError(401, 'Unauthorized Access');

    }

  
}
