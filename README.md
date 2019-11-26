# Hospital management system

[![CircleCI](https://circleci.com/gh/youngestdj/hospital-management.svg?style=svg)](https://circleci.com/gh/youngestdj/hospital-management) <a href="https://codeclimate.com/github/youngestdj/hospital-management/maintainability"><img src="https://api.codeclimate.com/v1/badges/403ae2a5b53072caa8a7/maintainability" /></a> <a href="https://codeclimate.com/github/youngestdj/hospital-management/test_coverage"><img src="https://api.codeclimate.com/v1/badges/403ae2a5b53072caa8a7/test_coverage" /></a>

##

#### Setup

- Install dependencies by running `composer install`
- Run tests by running `composer run test`

#### Seed database

- Run `php artisan db:seed` to create a root user. A link will be sent to the root user's email specified in the `.env` file

#### Static analysis

- Run `pecl install ast` to install the ast extension. Windows (http://windows.php.net/downloads/pecl/releases/ast/) https://github.com/nikic/php-ast
- Add `extension=ast.so` to php.ini on unix systems and `extension=php_ast.dll` to php.ini on windows systems
- Run `vendor/bin/phan --progress-bar -o analysis.txt` to run static analysis


### Features
* Users (Root, Admin, Doctor, Patient) can verify their account
* Users (Root, Admin, Doctor, Patient can log in
* Root can add admin account
* Admin can add doctor accounts
* Admin can add patient accounts
* Root can get an admin by email or id
* Root can get all admins
* Root, Admin or Doctor can get a patient by id or email
* Root can get all patients
* Root, Admin ot Doctor can get a doctor by id or email
* Root can get all doctors
* Doctor can get a patient's medical history
* Patients can view their prescriptions
* Patients can book appointments
* Patients can see their appointments
* Admin can edit appointments
* Doctors can see their appointments

### Docs
Visit the `/graphql-playground` endpoint in your browser to see the documentations