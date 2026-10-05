# 🎓 E-Learning RESTful API

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge\&logo=laravel\&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge\&logo=php\&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge\&logo=mysql\&logoColor=white)
![Sanctum](https://img.shields.io/badge/Auth-Laravel%20Sanctum-FF2D20?style=for-the-badge)
![Postman](https://img.shields.io/badge/API-Postman-FF6C37?style=for-the-badge\&logo=postman\&logoColor=white)

A **role-based E-Learning RESTful API** built with **Laravel 11**, **PHP 8.2+**, and **MySQL**.

The system provides separate workflows for **Admins, Instructors, and Students**, with authentication, authorization, course moderation, enrollments, lesson management, file uploads, notifications, and event-driven background processing.

The project focuses on building a structured backend with real-world business rules rather than simple CRUD operations.

---

## 📖 API Documentation

The API can be explored and manually tested using the provided Postman collection.

📁 **Postman Collection:** `Add your Postman link here`

The collection includes endpoints for:

* Authentication
* Users & Profiles
* Categories
* Courses
* Lessons
* Enrollments
* Notifications
* Authorization scenarios
* File uploads

---

# ✨ Features

## 🔐 Authentication

Authentication is implemented using **Laravel Sanctum**.

Users can:

* Register
* Login
* Logout
* Retrieve their authenticated profile
* Update profile information
* Update phone and personal information
* Upload profile images

Protected API routes require authentication through Sanctum.

---

# 👥 Role-Based Access Control

The platform has three main roles:

```text
                    ┌─────────────┐
                    │    Admin    │
                    └──────┬──────┘
                           │
                    Course Moderation
                           │
              ┌────────────┴────────────┐
              ↓                         ↓
       ┌─────────────┐           ┌─────────────┐
       │  Instructor │           │   Student   │
       └─────────────┘           └─────────────┘
```

### 👨‍💼 Admin

Admins can:

* Manage users
* Review submitted courses
* Approve or reject courses
* Manage courses where authorized
* Manage enrollments where authorized

### 👨‍🏫 Instructor

Instructors can:

* Create courses
* Submit courses for Admin approval
* Manage their own courses
* Add and manage lessons
* View students enrolled in their courses
* Manage course learning content

### 👨‍🎓 Student

Students can:

* Browse approved courses
* Enroll in courses
* View their enrolled courses
* Access lessons for enrolled courses
* Receive relevant notifications

---

# 🔄 Course Approval Workflow

Courses created by instructors are not immediately available to students.

They must first pass through an Admin moderation workflow.

```text
Instructor
    │
    ↓
Create Course
    │
    ↓
Status: Pending
    │
    ↓
CourseCreated Event
    │
    ↓
Queued Listener
    │
    ↓
🔔 Notify Admin
    │
    ↓
Admin Reviews Course
    │
    ├───────────────┐
    ↓               ↓
 Approved        Rejected
    │               │
    └───────┬───────┘
            ↓
 CourseReviewed Event
            │
            ↓
      Queued Listener
            │
            ↓
 🔔 Notify Instructor
```

### Course Visibility

Only approved/published courses are available in the public course catalog and can be accessed by students for enrollment.

This separates **course creation** from **course publication** and gives the Admin control over platform content.

---

# 📚 Course Management

Each course is associated with:

* An instructor
* A category
* Multiple lessons
* Enrolled students

### Course Lifecycle

```text
Instructor Creates Course
          ↓
Request Validation
          ↓
Course Created
          ↓
Pending Approval
          ↓
Admin Review
          ↓
Approved
          ↓
Course Published
          ↓
Students Can Enroll
          ↓
Students Access Learning Content
```

Instructors are restricted to managing their own courses through authorization rules.

---

# 🗂️ Categories

Courses are organized using categories.

```text
Category
   ├── Course
   ├── Course
   └── Course
```

Categories provide a structured way to organize and retrieve courses.

---

# 🎬 Lessons

Courses contain multiple lessons.

Lessons can include learning media such as:

* Video files
* Images
* Other supported resources

Instructors can manage lessons belonging to their own courses.

## 🔔 Lesson Notifications

When a new lesson is created, enrolled students are notified asynchronously.

```text
Instructor
    │
    ↓
Create Lesson
    │
    ↓
LessonCreated Event
    │
    ↓
Queued Listener
    │
    ↓
Fetch Enrolled Students
    │
    ↓
🔔 Notify Enrolled Students
```

Lesson access is protected according to the authenticated user's role and enrollment status.

---

# 🎓 Enrollment System

Students can enroll in available courses through the enrollment API.

The authenticated user's ID is taken directly from the authentication context rather than being accepted from the request body.

This prevents users from creating enrollments on behalf of other users.

## 🛡️ Enrollment Authorization

Enrollment operations are protected based on the user's role and relationship with the course.

Examples:

* Students can create their own enrollments.
* Students can view their enrolled courses.
* Instructors can view students enrolled in their courses.
* Administrators can manage enrollment records where authorized.
* Users cannot access unrelated enrollment records.

---

# 🚫 Duplicate Enrollment Prevention

A student cannot enroll in the same course multiple times.

```text
Student
   ↓
Request Enrollment
   ↓
Already Enrolled?
   ├── YES → Reject Request
   │
   └── NO  → Create Enrollment
```

The enrollment workflow checks the student's existing relationship with the course before creating a new record.

---

# 👨‍🏫 Instructor → Student Relationship

Instructors can view students enrolled in their own courses.

```text
Instructor
    ↓
Own Course
    ↓
Enrollments
    ↓
Students
```

This prevents instructors from accessing unrelated student/course relationships.

---

# 🔔 Notifications

The application uses Laravel Notifications together with events and queued listeners for important platform workflows.

### Course Submission

```text
Instructor Creates Course
          ↓
CourseCreated Event
          ↓
Queued Listener
          ↓
🔔 Admin Notification
```

### Course Review

```text
Admin Reviews Course
          ↓
CourseReviewed Event
          ↓
Queued Listener
          ↓
🔔 Instructor Notification
```

### New Lesson

```text
Instructor Creates Lesson
          ↓
LessonCreated Event
          ↓
Queued Listener
          ↓
🔔 Enrolled Students
```

Notifications can also be retrieved through the API for the authenticated user.

---


# 🛡️ Authorization & Middleware

Role-based middleware protects routes according to the authenticated user's role.

Authorization rules additionally verify relationships such as:

* Course ownership
* Enrollment ownership
* Lesson ownership
* Enrollment-based lesson access

Examples:

* Only approved courses are publicly visible.
* Students create their own enrollments.
* Instructors manage their own courses and lessons.
* Students can access learning content only when authorized.
* Admins have platform-level management capabilities.

---

# 📄 API Resources

Laravel **API Resources** are used to transform models into structured JSON responses.

This provides a consistent API response layer and prevents directly exposing database models.

Resources are used for major modules including:

* Users
* Courses
* Lessons
* Enrollments
* Notifications

---

# ✅ Form Requests & Validation

Dedicated Laravel **Form Request** classes are used to validate incoming requests.

Validation logic is separated from controllers to keep request handling clean and maintainable.

Validation covers:

* User registration
* Profile updates
* Course creation
* Lesson creation
* Enrollment requests
* File uploads

Invalid requests return Laravel validation responses.

---

# 📤 File Uploads

The API supports file uploads for user and learning content.

Supported use cases include:

* Profile images
* Course-related media
* Lesson images
* Lesson videos

Laravel Storage is used to manage uploaded files.

---

# 📑 Pagination

Pagination is implemented for collection-based endpoints to avoid unnecessarily large API responses.

Paginated resources include:

* Courses
* Lessons
* Enrollments
* Other collection endpoints

This makes the API more suitable for frontend applications.

---

# 🔗 Database Relationships

The application uses **Eloquent ORM relationships** to model the learning platform.

### User

```text
User
 ├── Profile
 ├── Courses
 ├── Enrollments
 └── Notifications
```

### Course

```text
Course
 ├── Instructor
 ├── Category
 ├── Lessons
 └── Enrollments
```

### Enrollment

```text
User
   ↕
Enrollment
   ↕
Course
```

These relationships allow the API to retrieve and authorize data based on ownership, roles, and enrollment status.

---

# 📂 Core API Modules

| Module            | Description                                            |
| ----------------- | ------------------------------------------------------ |
| 🔐 Authentication | Registration, login, logout, authenticated user access |
| 👤 Profiles       | Profile information, phone, and image management       |
| 👥 Users          | Role-based user management                             |
| 🗂️ Categories    | Course categorization                                  |
| 📚 Courses        | Course creation, moderation, approval, and management  |
| 🎬 Lessons        | Lesson and learning-media management                   |
| 🎓 Enrollments    | Student enrollment and enrollment management           |
| 🔔 Notifications  | User notifications and event-driven updates            |

---

# 🧪 API Testing

The API can be manually tested using the provided **Postman Collection**.

The collection covers:

* Authentication
* Role-based endpoints
* Course approval workflow
* Course management
* Lesson management
* Enrollment
* Authorization scenarios
* File uploads
* Notifications

---

# 🚀 Getting Started

## 1. Clone the Repository

```bash
git clone https://github.com/saraomar-dev/Laravel-E-Learning-API.git
cd Laravel-E-Learning-API
```

## 2. Install Dependencies

```bash
composer install
```

## 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database and required environment variables inside `.env`.

## 4. Run Migrations

```bash
php artisan migrate
```

## 5. Create Storage Link

```bash
php artisan storage:link
```

## 6. Start the Application

### Terminal 1 — Laravel Server

```bash
php artisan serve
```

### Terminal 2 — Queue Worker

```bash
php artisan queue:work
```

The API will be available at:

```text
http://127.0.0.1:8000
```

---

# 🧰 Tech Stack

* **PHP 8.2+**
* **Laravel 11**
* **MySQL 8**
* **Laravel Sanctum**
* **Eloquent ORM**
* **Laravel Middleware**
* **Laravel API Resources**
* **Laravel Form Requests**
* **Laravel Notifications**
* **Laravel Events & Listeners**
* **Laravel Queues**
* **Laravel Storage**
* **Postman**

---

# 📌 What This Project Demonstrates

This project demonstrates practical backend development through a complete learning-platform workflow.

### Backend Architecture

* RESTful API development
* MVC architecture
* Eloquent relationships
* API Resources
* Form Requests
* Middleware
* Events & Listeners
* Queued background processing

### Authentication & Authorization

* Laravel Sanctum authentication
* Role-based access control
* Ownership authorization
* Enrollment-based authorization
* Protected learning resources

### Business Logic

* Course moderation workflow
* Instructor course ownership
* Student enrollment
* Duplicate enrollment prevention
* Enrollment-based lesson access
* Event-driven notifications

### API Development

* Structured JSON responses
* Pagination
* Validation
* File uploads
* Notification endpoints
* Postman API testing

---

# 🚧 Future Improvements

Possible future extensions include:

* Automated Feature / Integration Tests
* Advanced course search and filtering
* Course sorting
* Course reviews and ratings
* Student progress tracking
* Lesson completion tracking
* Certificates
* Payment integration
* Redis-based production queues
* Dockerized development environment
* CI/CD pipeline
* Production deployment

---

# 👩‍💻 Author

**Sara Omar**

Backend Developer focused on building structured, secure, and scalable RESTful APIs with **Laravel and PHP**.

