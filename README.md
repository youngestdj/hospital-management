# hospital-management
Hospital management system

[![CircleCI](https://circleci.com/gh/youngestdj/hospital-management.svg?style=svg)](https://circleci.com/gh/youngestdj/hospital-management) <a href="https://codeclimate.com/github/youngestdj/hospital-management/maintainability"><img src="https://api.codeclimate.com/v1/badges/403ae2a5b53072caa8a7/maintainability" /></a> <a href="https://codeclimate.com/github/youngestdj/hospital-management/test_coverage"><img src="https://api.codeclimate.com/v1/badges/403ae2a5b53072caa8a7/test_coverage" /></a>


* Install dependencies by running `composer install`
* Run tests by running `composer run test`

### Seed database
Run `php artisan db:seed` to create a root user. A link will be sent to the root user's email specified in the `.env` file

### Static analysis
* Run `pecl install ast` to install the ast extension. Windows (http://windows.php.net/downloads/pecl/releases/ast/) https://github.com/nikic/php-ast
* Add `extension=ast.so` to php.ini on unix systems and `extension=php_ast.dll` to php.ini on windows systems
* Run `vendor/bin/phan --progress-bar -o analysis.txt` to run static analysis

### Verify Root user
#### Mutation
```
mutation {
  verifyRoot(key: "verificationKey", password: "abcdef")
}
```
`{key}` verificationKey sent to the email.  
`{password}` Password for root user. Must be six characters or more
#### Response
```
{
  "data": {
    "verifyRoot": "Root User has been verified. You can now log in with your email and password."
  }
}
```
```
{
  "data": {
    "verifyRoot": "Invalid verification key."
  }
}
```

### Login
#### Mutation
```
mutation {
  login(email: "email@example.com", password: "password", user: "Admin") {
    id,
    email,
    token,
    firstname,
    lastname
  }
}
```
`{email}` User email  
`{password}` user password  
`{user}` `Root` `Admin`, `Patient`, `Doctor`

#### Response
```
{
  "data": {
    "login": {
      "id": 1,
      "email": "email@example.com",
      "token": "eyJ0eXAiOiJKV1QirgCJhbGciOiJIUzI1Nisgsfs",
      "firstname": "Root",
      "lastname": "User"
    }
  }
}
```
```
{
  "errors": [
    {
      "message": "Invalid email or password.",
      "extensions": {
        "error": "Login failed.",
        "category": "custom"
      },
      "locations": [
        {
          "line": 2,
          "column": 3
        }
      ],
      "path": [
        "login"
      ]
    }
  ],
  "data": {
    "login": null
  }
}
```