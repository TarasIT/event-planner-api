# Event Planner API

Event Planner is a Laravel-based API designed to manage events. It allows users to create, read, update, and delete events, as well as manage event pictures via the Cloudinary service. Additionally, the API provides authentication features, including email verification and Google sign-up, utilizing Laravel Sanctum. Also the API implements reset password feature.

## Features

-   **Event Management**:
    -   Create, read, update, and delete events.
    -   Store and manage event pictures in Cloudinary.
-   **Authentication**:
    -   User signup and login with email verification.
    -   Google sign-up.
    -   Password reset functionality.

## Event Fields

The API manages the following event fields:

-   `title`
-   `description`
-   `date`
-   `time`
-   `location`
-   `category`
-   `picture`
-   `priority`

## Installation

1. Clone the repository:

    ```sh
    git clone https://github.com/your-username/event-planner-api.git
    cd event-planner-api
    ```

2. Install dependencies:

    ```sh
    composer install
    npm install
    ```

3. Set up the environment variables:

    ```sh
    cp .env.example .env
    ```

    Edit the `.env` file to set up your database, Google and Cloudinary configuration.

4. Generate the application key:

    ```sh
    php artisan key:generate
    ```

5. Run migrations:

    ```sh
    php artisan migrate
    ```

6. Serve the application:
    ```sh
    php artisan serve
    ```

## Contributing

1. Fork the repository.
2. Create your feature branch:
    ```sh
    git checkout -b feature/your-feature
    ```
3. Commit your changes:
    ```sh
    git commit -am 'Add some feature'
    ```
4. Push to the branch:
    ```sh
    git push origin feature/your-feature
    ```
5. Open a pull request.

## License

This project is licensed under the MIT License.
