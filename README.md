# PHP Note-Taking Web Application

A user-friendly note-taking web application built with PHP, MySQLi, Js, and CSS. This application allows users to:

* Register and log in securely
* Create, edit, and delete notes
* Update profile information
* Connect with friends and send messages

## Features

* User Authentication
* Note Management
* Profile Management
* Friend System
* Messaging

## File Structure

* `index.php`: Main landing page, manages sessions, displays user profile and notes.
* `register.php`: User registration form.
* `login.php`: User login form.
* `logout.php`: Handles user logout.
* `add_note.php`: Adds a new note.
* `edit_note.php`: Edits an existing note.
* `delete_note.php`: Deletes a note.
* `edit_profile.php`: Allows users to edit profile information.
* `menu.php`: Navigation menu.
* `friends.php`: Displays a list of user's friends.
* `add_friend.php`: Allows users to search for and add friends.
* `remove_friend.php`: Handles friend removal.
* `messages.php`: Manages sending and receiving messages between friends.
* `send_messages.php`: Handles sending messages.
* `choose_friend.php`: Displays potential friends for the user to add.
* `process_request.php`: Processes friend requests (accept/reject).
* `message.js`: JavaScript for managing message sending and loading.

## Setup Instructions

1. **Clone the Repository**

   ```bash
   git clone https://github.com/your-username/note-taking-app.git
   ```

2. **Setup Database**

   * Import the `database.sql` file into your MySQL database.
   * Update the `db_config.php` file with your database credentials.

3. **Configure XAMPP**

   * Move the project folder to your XAMPP htdocs directory.
   * Start Apache and MySQL from the XAMPP control panel.

4. **Access the Application**

   * To Access Locally open your web browser and go to http://localhost/note-taking-app.
   * To Access the currently hosted website open your web browser and go to https://notetakersph.free.nf/menu.php.

## Database Structure

**Users Table**

* user_id: INT (Primary Key, Auto Increment)
* name: VARCHAR(255)
* lastname: VARCHAR(255)
* email: VARCHAR(255) (Unique)
* birthdate: DATE
* password: VARCHAR(255)
* created_at: TIMESTAMP
* profile_image: BLOB

**Notes Table**

* id: INT (Primary Key, Auto Increment)
* user_id: INT (Foreign Key references Users)
* title: VARCHAR(255)
* content: TEXT
* created_at: TIMESTAMP
* image_url: VARCHAR(255)

**Friends Table**

* id: INT (Primary Key, Auto Increment)
* user_id: INT (Foreign Key references Users)
* friend_id: INT (Foreign Key references Users)
* created_at: TIMESTAMP

**Friend Requests Table**

* request_id: INT (Primary Key, Auto Increment)
* sender_id: INT (Foreign Key references Users)
* receiver_id: INT (Foreign Key references Users)
* status: ENUM('pending', 'accepted', 'rejected') (Default 'pending')
* created_at: TIMESTAMP

**Messages Table**

* id: INT (Primary Key, Auto Increment)
* sender_id: INT (Foreign Key references Users)
* receiver_id: INT (Foreign Key references Users)
* message: TEXT
* sent_at: TIMESTAMP
* status: ENUM('sent', 'seen') (Default 'sent')
* image_url: VARCHAR(255)
