<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BooksController extends Controller
{
    use ApiHelpers;

 public function book(Request $request): \Illuminate\Http\JsonResponse
    {

            $perPage = $request->input('page', 5);
            if (!is_numeric($perPage)) {
              $perPage = 5;
            }

            $books = Book::withCount('likes')->orderBy('likes_count', 'desc')->paginate($perPage,2,1);
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

    public function paginate($items,$perPage,$pageStart=1)
    {

        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;

        // Get only the items you need using array_slice
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, true);
        return new LengthAwarePaginator($itemsForCurrentPage, count($items),
            perPage,Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));
    }

    public function createBook(Request $request): \Illuminate\Http\JsonResponse
    {

       $user = $request->user();
       if ($this->isAdmin($user)){
           $validator = Validator::make($request->all(), $this->postValidationRules());
           if ($validator->passes()){
               if(Auth::user()->role==1){
                // Create New Book
               $book = new Book();
               $book->user_id = Auth::user()->id;
               $book->title = $request->input('title');
               $book->slug = Str::slug($request->input('title'));
               $book->content = $request->input('content');
               $book->save();
               $book->user;

               return $this->onSuccess($book, 'Book Created');
               
               }else{
                return $this->onError(400, 'Only Admin Access needed');
               }
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
            if(Auth::user()->role==1){
                // update  Book
                $book = Book::find($request->id);
                $book->title = $request->input('title');
                $book->content = $request->input('content');
                $book->update();

                return $this->onSuccess($book, 'Book Updated');
            }else{
                return $this->onError(400, 'Only Admin Access needed');
               }
            }
            return $this->onError(400, $validator->errors());
        }

        return $this->onError(401, 'Unauthorized Access');
    }

    public function deleteBook(Request $request): \Illuminate\Http\JsonResponse
    {

        $user = $request->user();
        if ($this->isAdmin($user) ){
            if(Auth::user()->role==1){
            $book = Book::find($request->id); // Find the id of the post passed
            $book->delete();         // Delete the specific post data
            if (!empty($book)){
                return $this->onSuccess($book, 'Book Deleted');
            }
            return $this->onError(404, 'Book Not Found');
           }else{
            return $this->onError(400, 'Only Admin Access needed');
           }
        }
        return $this->onError(401, 'Unauthorized Access');
    }

    public function createUser(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if ($this->isAdmin($user)){
            $validator = Validator::make($request->all(), $this->userValidatedRules());
            if ($validator->passes()){
                if(Auth::user()->role==1){
                // Create New Writer
                User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'role' => 2,
                    'password' => Hash::make($request->input('password'))
                ]);

                $writerToken =  $user->createToken('auth_token', ['writer'])->plainTextToken;
                return $this->onSuccess($writerToken, 'User Created With Writer Privilege');
               }else{
                return $this->onError(400, 'Only Admin Access needed');
               }
            }
            return $this->onError(400, $validator->errors());
        }

        return $this->onError(401, 'Unauthorized Access');

    }


}
