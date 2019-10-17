# hospital-management
Hospital management system

[![CircleCI](https://circleci.com/gh/youngestdj/hospital-management.svg?style=svg)](https://circleci.com/gh/youngestdj/hospital-management) <a href="https://codeclimate.com/github/youngestdj/hospital-management/maintainability"><img src="https://api.codeclimate.com/v1/badges/403ae2a5b53072caa8a7/maintainability" /></a> <a href="https://codeclimate.com/github/youngestdj/hospital-management/test_coverage"><img src="https://api.codeclimate.com/v1/badges/403ae2a5b53072caa8a7/test_coverage" /></a>


* Install dependencies by running `composer install`
* Run tests by running `composer run test`

### Seed database
Run `php artisan db:seed` to create a root user. A link will be sent to the root user's email specified in the `.env` file

### Static analysis
* Run `pecl install ast` to install the ast extension
* Run `vendor/bin/phan --progress-bar -o analysis.txt` to run static analysis

### Verify Root user
```
mutation {
  verifyRoot(key: "verificationKey", password: "abcdef")
}
```
