<?php

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\Auth\API\AuthController;
use App\Http\Controllers\BooksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//user login
Route::post('login', [AuthController::class, 'login']); // user login
Route::post('register',[AuthController::class,'register']);



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:sanctum'], function() {

    //user management
    Route::get('users', [AuthController::class, 'getUsers']); // get all users
    Route::post('users/create', [BooksController::class, 'createUser']); // add a new user with writer scope
    Route::post('users/subscriber', [BooksController::class, 'createSubscriber']); // add a new user with subscriber scope
    Route::delete('users/delete', [AuthController::class, 'deleteUser']); // delete a user
    Route::post('users/update',[AuthController::class,'updateUserInfo']); //update user info
    Route::get('logout',[AuthController::class,'logout']);


    //books
    Route::get('books', [BooksController::class, 'book']); // list all books
    Route::post('books/create', [BooksController::class, 'createBook']); // add a new book
    Route::put('books/update', [BooksController::class, 'updateBook']); // updating a book
    Route::delete('books/delete', [BooksController::class, 'deleteBook']); // delete a book

    //comments
    Route::post('comments/create',[CommentsController::class, 'create']);
    Route::post('books/comments',[CommentsController::class, 'comments']);
    Route::post('comments/delete',[CommentsController::class, 'delete']);

    //like
    Route::post('books/like',[LikesController::class, 'like']);

    //favourites
    Route::post('books/mark',[FavouritesController::class, 'favourite']);

});
