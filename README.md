Steps needed for running the application stack: 

.env contents:
DB_HOST=db
DB_NAME=<<insert database name>>
DB_USER=<<insert user for your database>>
DB_PASS=<<insert database password>>
DB_CHARSET=utf8mb4

REGISTRATION_KEY=key4use
TEST_ADMIN=<<insert username you want to be insereted into database>>
TEST_PW=<<insert password for set username>>

Build and start the application stack: docker compose up --build -d

Application should be accessible on browser on: http://localhost

Login with credentials from .env
