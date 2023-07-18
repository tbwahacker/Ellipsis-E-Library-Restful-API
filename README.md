Documentation

1). Tech stack for backend

- Laravel (php)
- Tailwind for Auth interface
- Mysql Database used



2). Installation
  
  # Requirements (If you don want to run locally please jump to ====> step2)

  ## =====> Step1

First of all , rename .env copy to .env then run `composer install`

- run  `php artisan generate:key` to generate app key inside .env file.

- you may also need to run `composer update` in case you got erros.

- replace database configs with your own.

- run your xampp or wampp for services

- For testing, connect android hotspot with your pc
  open cmd or terminal and run command ipconfig or ifconfig base on your terminal
  after running command copy the ipv4 address and it will be as your hostIp

- now run server, where hostIP:LaravelPort
  use command below as example ,
  > php -S 192.168.46.105:8000 -t public  


 ## ========> step2

- open your app for easy testing the Restfull API  

- note: Do not forget to create the admin account through the web link (for the first time)
 http://192.168.46.105:8000/login


3). API end-points

## User Management
- admin can create user (POST)
http://192.168.46.105:8000/api/users/create

- admin can Edit user (POST)
http://192.168.46.105:8000/api/users/update

- admin can delete user (DELETE)
http://192.168.46.105:8000/api/users/delete

- admin can get all users through
http://192.168.46.105:8000/api/users

- App user login (POST)
http://192.168.46.105:8000/api/login

- App user logout (GET)
http://192.168.46.105:8000/api/logout


## Book
- create book (POST)
http://192.168.46.105:8000/api/books/create?title=booktitle&content=book_description_or_author_etc

- get all books by paging (GET)
http://192.168.46.105:8000/api/books?page=4

-admin can delete book (where id = book_id) (DELETE)
http://192.168.46.105:8000/api/books/delete?id=4

- admin can Edit book (where id = book_id) (PUT)
http://192.168.46.105:8000/api/books/update?id=&content=&title=


## Comments
- create comments (where id = book_id) (POST)
http://192.168.46.105:8000/api/comments/create?id=1&comment=your_comment

- get all comments (where id = book_id) (GET)
http://192.168.46.105:8000/api/books/comments?id=4

-admin can delete comments (where id = comment_id) (DELETE)
http://192.168.46.105:8000/api/comments/delete?id=4


## Likes
- Like and unlike a book (POST)
http://192.168.46.105:8000/api/books/like?book_id=2&user_id=5

- Mark and Unmark a favourite book (POST)
http://192.168.46.105:8000/api/books/mark?book_id=2&user_id=5


