# Connect-the-Dots Game Server

This repository contains the source code for a Connect-the-Dots game server built using Laravel 8 and MySQL. The game server allows two players to take turns drawing octilinear lines to connect nodes on a 4x4 grid. The objective of the game is to avoid making the last move, as the player who draws the last line is the loser.

## Table of Contents

- [Coding Convention](#coding-convention)
- [Database Structure](#database-structure)
- [Game Logic](#game-logic)
- [Session Management](#session-management)
- [HTTP API Development](#http-api-development)
- [Response Handling](#response-handling)
- [Important Files](#important-files)
- [Outputs](#outputs)

## Coding Convention

The codebase follows Laravel 8's coding conventions and best practices. This includes adhering to the SOLID design principles and utilizing Laravel's built-in features for routing, middleware, and database operations. The code is organized into controllers, services, and responses for modularity and maintainability.

## Database Structure

The database schema consists of three main tables:

1. `games`: This table stores information about each game, including its status, player IDs, and whose turn it is.

2. `moves`: This table records the moves made in each game, storing the game ID, coordinates (x, y), and player ID for each move.

3. `players`: This table manages player information and assigns a unique token for player identification.

Foreign key constraints are used to maintain referential integrity between tables, ensuring data consistency.

Migrations for these tables can be found in the `database/migrations` directory.

## Game Logic

The game logic is implemented in the `GameReferee` service. It enforces the rules of the game, including:

- Validating moves to ensure they do not violate game rules.
- Checking for a winning condition (all nodes connected) and game completion.
- Preventing moves that intersect with existing lines.
- Enforcing the rule that no node can be visited twice.

## Session Management

Sessions are used for managing game states. The current game ID and player ID are stored in the session, allowing the application to track the active game and player turn.

## HTTP API Development

The game server exposes HTTP APIs for game initialization and move placement. These APIs are handled by the `GameController`. The following APIs are available:

- `initialize`: Initializes a new game.
- `nodeClicked`: Allows players to place their moves.
- `reportError`: Allows reporting errors or issues.

API endpoints are defined in the routes file `routes/web.php`, following RESTful conventions.

## Response Handling

Responses for API endpoints are handled using custom response classes located in the `app/Http/Responses` directory. These response classes ensure consistent and informative responses to API requests, making it easier to handle errors and game outcomes on the client side.

## Important Files

The following files and directories are crucial for understanding the codebase:

- `app/Http/Controllers/GameController.php`: Contains the main controller for handling game-related actions.
- `app/Services/GameReferee.php`: Implements the game logic and rules.
- `app/Http/Responses`: Contains custom response classes for API responses.
- `app/Http/Requests/MoveValidationRequest.php`: Validates move requests.
- `database/migrations`: Contains database migration files for creating the required tables.

## Outputs

The `output` directory contains output files such as images and JSON files generated during the execution of the game server. These outputs can be used for testing and analysis.

Feel free to explore the codebase and run the game server using Laravel the client side code is also integrated with the application for testing in `public/js/init.js`