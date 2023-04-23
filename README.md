# projecta
System for Projects and Tasks management


## Task Spec
Create a system to control projects and tasks (design - front-end is not important).
The project should have a title, description, status (new, pending, failed, done), and duration.
The project should have additional fields for clients and companies.
Every project can have multiple tasks and the status and time frame for the projects is related to the tasks included.
Create validation that one of the fields is populated - client or company.

Create Rest API with CRUD operation for projects and tasks.
Delete of the project and tasks should be "soft delete" - mark as deleted, but not delete from the database.
API should always return HTTP code 200, but with different "code" in the response - code -> 0 for success and code -> -1 for error.

Example:  
{  
"code": 0,  
"data": ... some data  
"validation_errors: []  
}  

Create pagination on list operation (default is 20 results per page).
Create a simple visual template that presents data in the tables (including pagination).

Language: PHP  
Framework: Symfony  
Database: PostgreSQL.  
Use ORM for DB operations.  

## Installation
System requirements: PHP 8.2, Symfony 6.2

1. Clone the repo
   ```sh
   git clone https://github.com/indjeto/projecta.git
   cd projecta
   ```
2. Install packages
   ```sh
   composer install
   ```
3. Run local PostgreSQL server
   ```sh
   docker compose up -d
   ```
4. Prepare databse
   ```sh
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
5. Run the app
   ```sh
   symfony serve
   ```
  The App should be accessible on http://localhost:8000

6. You can use the Postman collection to try the API.

