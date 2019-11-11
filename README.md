# Hospital-management

[![CircleCI](https://circleci.com/gh/youngestdj/hospital-management.svg?style=svg)](https://circleci.com/gh/youngestdj/hospital-management) <a href="https://codeclimate.com/github/youngestdj/hospital-management/maintainability"><img src="https://api.codeclimate.com/v1/badges/403ae2a5b53072caa8a7/maintainability" /></a> <a href="https://codeclimate.com/github/youngestdj/hospital-management/test_coverage"><img src="https://api.codeclimate.com/v1/badges/403ae2a5b53072caa8a7/test_coverage" /></a>

## Hospital management system

### Setup

- Install dependencies by running `composer install`
- Run tests by running `composer run test`

### Seed database

- Run `php artisan db:seed` to create a root user. A link will be sent to the root user's email specified in the `.env` file

### Static analysis

- Run `pecl install ast` to install the ast extension. Windows (http://windows.php.net/downloads/pecl/releases/ast/) https://github.com/nikic/php-ast
- Add `extension=ast.so` to php.ini on unix systems and `extension=php_ast.dll` to php.ini on windows systems
- Run `vendor/bin/phan --progress-bar -o analysis.txt` to run static analysis

## Docs

### Verify User

#### Mutation

```
mutation {
  verifyUser(key: "verificationKey", password: "abcdef", user: "User")
}
```

#### Parameters

`{key}` verificationKey sent to the email.  
`{password}` Password for root user. Must be six characters or more  
`{user}` `Root`, `Admin`, `Doctor`, `Patient`

#### Response

```
{
  "data": {
    "verifyUser": "Account has been verified. You can now log in with your email and password."
  }
}
```

```
{
  "data": {
    "verifyUser": "Invalid verification key."
  }
}
```

### Login

#### Mutation

```
mutation {
  login(email: "email@example.com", password: "password", user: "User") {
    id,
    email,
    token,
    firstname,
    lastname
  }
}
```

#### Parameters

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

### Add user

#### Add admin

##### Headers

```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Root token

##### Mutation

```
mutation {
  addAdmin(email: "email@example.com", firstname: "Firstname", lastname: "Lastname") {
    id,
    email,
    firstname,
    lastname
  }
}
```

##### Parameters

`{email}` Admin email  
`{firstname}` Firstname  
`{lastname}` Lastname

##### Response

```
{
  "data": {
    "addAdmin": {
      "id": 3,
      "email": "email@example.com",
      "firstname": "Firstname",
      "lastname": "Lastname"
    }
  }
}
```

#### Add doctor

##### Headers

```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Admin token

##### Mutation

```
mutation {
  addDoctor(
    email: "email@example.com",
    firstname: "Firstname",
    lastname: "Lastname",
    phone: "08012345678",
    gender: "male",
    dob: "09-08-1988",
    specialization: "dermatologist"
  ) {
    id,
    email,
    firstname,
    lastname,
    dob,
    specialization
  }
}
```

##### Parameters

`{email}` Doctor email  
`{firstname}` Firstname  
`{lastname}` Lastname  
`{phone}` Phone number  
`{gender}` `male`, `female`, `other`  
`{dob}` dd-mm-yyyy  
`{specialization}` Specialization

##### Response

```
{
  "data": {
    "addDoctor": {
      "id": 4,
      "email": "email@example.com",
      "firstname": "Firstname",
      "lastname": "Lastname"
      "dob": "09-08-1988",
      "specialization": "dermatologist"
    }
  }
}
```

#### Add patient

##### Headers

```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Admin token

##### Mutation

```
mutation {
  addPatient(
    email: "email@example.com",
    firstname: "Firstname",
    lastname: "Lastname",
    phone: "09012345678",
    gender: "male",
    dob: "09-08-1988",
    occupation: "Farmer",
    address: "1, Ojota, Lagos",
    nationality: "Nigerian",
    marital_status: "single",
    religion: "Christianity",
    ethnicity: "Yoruba"
  ) {
    id,
    firstname,
    email,
    lastname,
    phone,
    gender,
    dob,
    occupation,
    address,
    nationality,
    marital_status,
    religion,
    ethnicity
  }
}

```

##### Parameters

`{email}` Patient email _(optional)_  
`{firstname}` Firstname  
`{lastname}` Lastname  
`{phone}` Phone number  
`{gender}` `male`, `female`, `other`  
`{dob}` Patient's date of birth. dd-mm-yyyy  
`{occupation}` Occupation  
`{address}` Address  
`{nationality}` Nationality
`{marital_status}` Marital status. `single`, `married`, `divorced` or `widowed`  
`{religion}` Religion  
`{ethnicity}` Ethnicity  

##### Response

```
{
  "data": {
    "addPatient": {
      "email": "email@example.com"
      "id": 15,
      "firstname": "Firstname",
      "lastname": "Lastname",
      "phone": "09012345678",
      "gender": "male",
      "dob": "09-08-1988",
      "occupation": "Farmer",
      "address": "1, Ojota, Lagos",
      "nationality": "Nigerian",
      "marital_status": "single",
      "religion": "Christianity",
      "ethnicity": "Yoruba" 
    }
  }
}
```

### Get users
#### Get an admin by email or id
##### Headers
```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Root token
##### Query
```
{
  admin (email: "admin@email.com") {
    id
    firstname,
    lastname,
    email
  }
}
```
```
{
  admin (id: 1) {
    id
    firstname,
    lastname,
    email
  }
}
```
##### Response
```
{
  "data": {
    "admin": {
      "id": 2,
      "firstname": "Firstname",
      "lastname": "Lastname",
      "email": "admin@email.com"
    }
  }
}
```

#### Get all admins
##### Headers
```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Root token
##### Query
```
{
  admins(first: 5) {
    paginatorInfo {
      total,
      hasMorePages,
      currentPage,
      lastPage
    }
    data {
    id
    firstname
    }
  }
}

```

##### Response
```
{
  "data": {
    "admins": {
      "paginatorInfo": {
        "total": 2,
        "hasMorePages": false,
        "currentPage": 1,
        "lastPage": 1
      },
      "data": [
        {
          "id": 1,
          "firstname": "Root"
        },
        {
          "id": 2,
          "firstname": "Firstname"
        }
      ]
    }
  }
}
```

#### Get a patient by email or id
##### Headers
```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Root token, Admin token, Doctor token
##### Query
```
{
  patient (id: 14) {
    id
    firstname,
    lastname,
    email
  }
}

```
```
{
  patient (email: "email@example.com") {
    id
    firstname,
    lastname,
    email
  }
}

```
##### Response
```
{
  "data": {
    "patient": {
      "id": 14,
      "firstname": "Firstname",
      "lastname": "Lastname",
      "email": "email@example.com"
    }
  }
}
```

#### Get all patients
##### Headers
```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Root token
##### Query
```
{ 
  patients(first: 5) {
    paginatorInfo {
      total,
      hasMorePages,
      currentPage,
      lastPage
    }
    data {
      id
      firstname,
    }
  }
}
```

##### Response
```
{
  "data": {
    "patients": {
      "paginatorInfo": {
        "total": 2,
        "hasMorePages": false,
        "currentPage": 1,
        "lastPage": 1
      },
      "data": [
        {
          "id": 1,
          "firstname": "Root"
        },
        {
          "id": 2,
          "firstname": "Firstname"
        }
      ]
    }
  }
}
```


#### Get a doctor by email or id
##### Headers
```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Root token, Admin token, Doctor token
##### Query
```
{
  doctor (id: 14) {
    id
    firstname,
    lastname,
    email
  }
}

```
```
{
  doctor (email: "email@example.com") {
    id
    firstname,
    lastname,
    email
  }
}

```
##### Response
```
{
  "data": {
    "doctor": {
      "id": 14,
      "firstname": "Firstname",
      "lastname": "Lastname",
      "email": "email@example.com"
    }
  }
}
```

#### Get all doctors
##### Headers
```
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
```

`{Authorization}:` Root token
##### Query
```
{ 
  doctors(first: 5) {
    paginatorInfo {
      total,
      hasMorePages,
      currentPage,
      lastPage
    }
    data {
      id
      firstname,
    }
  }
}
```

##### Response
```
{
  "data": {
    "doctors": {
      "paginatorInfo": {
        "total": 2,
        "hasMorePages": false,
        "currentPage": 1,
        "lastPage": 1
      },
      "data": [
        {
          "id": 1,
          "firstname": "Root"
        },
        {
          "id": 2,
          "firstname": "Firstname"
        }
      ]
    }
  }
}
```
