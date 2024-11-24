A simple and user-friendly Todo application built with Symfony 6.4 and powered by a MySQL database. This application allows users to manage their tasks with the following features:
  - Add Tasks: Quickly create new tasks to stay organized.
  - Update Tasks: Edit task details as needed.
  - Delete Tasks: Remove tasks that are no longer needed.
  - Mark Tasks as Finished: Keep track of completed tasks with a single click.

Installation:
 - Clone this repository
 - set DATABASE_URL in .env - replace #username#, #password# and change serverVersion if needed
 - run these commands
   - composer install
   - php bin/console doctrine:database:create
   - php bin/console doctrine:migrations:migrate
   - symfony server:start
   - go to localhost:8000/
