# 🎓 E-Learning RESTful API

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge\&logo=laravel\&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge\&logo=php\&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge\&logo=mysql\&logoColor=white)](https://mysql.com)
[![Postman](https://img.shields.io/badge/Postman-Documented-FF6C37?style=for-the-badge\&logo=postman\&logoColor=white)](https://postman.com)

A role-based **E-Learning RESTful API** built with **Laravel 11**, **PHP 8.2+**, and **MySQL**, designed to manage an online learning platform with separate Admin, Instructor, and Student workflows.

The application implements authentication, role-based authorization, instructor approval, course management, lessons, student enrollment, file uploads, notifications, pagination, API Resources, Form Requests, and protected access to learning resources.

---

## 📖 API Documentation

The API can be explored and manually tested using the provided Postman collection.

* 📁 **Postman Collection:** `Add your Postman link here`

The collection covers authentication, users, courses, lessons, enrollments, and notifications.

---

# ✨ Core Features

## 🔐 Authentication

The API provides secure authentication for platform users.

Users can:

* Register.
* Login.
* Logout.
* Retrieve their authenticated profile.
* Update profile information.
* Upload profile images.
* Update phone and personal information.

Authentication is implemented using **Laravel Sanctum** and protected API routes.

---

# 👥 Role-Based System

The platform has three main roles:

```text
                 ┌─────────────┐
                 │    Admin    │
                 └──────┬──────┘
                        │
              Approves / Manages
                        │
          ┌─────────────┴─────────────┐
          ↓                           ↓
   ┌─────────────┐             ┌─────────────┐
   │  Instructor │             │   Student   │
   └─────────────┘             └─────────────┘
```

### 👨‍💼 Admin

Responsible for platform-level management, including:

* Managing users.
* Reviewing instructor applications.
* Approving or rejecting instructors.
* Managing courses where authorized.
* Managing enrollments where authorized.

### 👨‍🏫 Instructor

Instructors can:

* Create courses after receiving approval.
* Manage their own courses.
* Add and manage lessons.
* View students enrolled in their courses.
* Manage course learning content.

### 👨‍🎓 Student

Students can:

* Browse available courses.
* Enroll in courses.
* View their enrolled courses.
* Access lessons for courses they are enrolled in.
* Receive relevant notifications.

---

# 🔄 Instructor Approval Workflow

One of the main business workflows of the application is the instructor approval process.

An instructor cannot immediately operate as an active course creator.

### Workflow

```text
Instructor Registration
        ↓
Instructor Account
        ↓
Pending Approval
        ↓
Admin Reviews Application
        ↓
   ┌────┴────┐
   ↓         ↓
Approved   Rejected
   ↓
Instructor Can
Create Courses
```

The approval state controls whether an instructor is allowed to perform instructor-specific operations.

### Notifications

Important approval actions trigger notifications so the instructor can be informed about the result of the review.

---

# 📚 Course Management

Courses are the main learning resources of the platform.

A course is associated with:

* An instructor.
* A category.
* Multiple lessons.
* Enrolled students.

### Course Workflow

```text
Approved Instructor
        ↓
Create Course
        ↓
Validate Course Data
        ↓
Course Created
        ↓
Add Lessons
        ↓
Students Can Enroll
        ↓
Students Access Learning Content
```

Courses are protected using authentication and authorization rules to ensure that instructors can manage their own courses.

---

# 🗂️ Categories

Courses are organized through categories.

The system supports the relationship:

```text
Category
   │
   ├── Course
   ├── Course
   └── Course
```

This provides a structured way to organize and retrieve courses.

---

# 🎬 Lessons

Courses contain multiple lessons.

Lessons can include uploaded learning media such as:

* Video files.
* Images.
* Other supported lesson resources.

### Lesson Workflow

```text
Instructor
    ↓
Select Own Course
    ↓
Create Lesson
    ↓
Upload Lesson Media
    ↓
Lesson Belongs to Course
    ↓
Enrolled Student
    ↓
Access Learning Content
```

Lesson access is controlled according to the user's role and relationship with the course.

---

# 🎓 Enrollment System

Students can enroll in courses through the enrollment API.

Only students are allowed to create their own enrollments.

The authenticated user's ID is taken from the authentication context rather than being accepted from the request body.

This prevents a user from creating an enrollment on behalf of another user.

---

## 🛡️ Enrollment Authorization

Enrollment operations are protected according to the user's role and relationship with the course.

Examples include:

* Students can create enrollments.
* Students can view their enrolled courses.
* Instructors can view students enrolled in their courses.
* Administrators can manage enrollment records where authorized.
* Users cannot access unrelated enrollment records.

---

# 🚫 Duplicate Enrollment Prevention

A student should not be able to enroll in the same course multiple times.

The enrollment workflow validates the student's existing relationship with the course before creating a new enrollment.

```text
Student
   ↓
Request Enrollment
   ↓
Already Enrolled?
 ┌──────┴──────┐
 ↓             ↓
 YES           NO
 ↓             ↓
Reject       Create
Request      Enrollment
```

This keeps enrollment data consistent and prevents duplicate records.

---

# 👨‍🏫 Instructor → Student Workflow

Instructors can view the students enrolled in their own courses.

The relationship follows:

```text
Instructor
    ↓
Own Course
    ↓
Enrollments
    ↓
Students
```

This allows instructors to manage and review the learners participating in their courses without exposing unrelated student/course relationships.

---

# 🔔 Notifications

The application includes a notification system for important platform events.

Notifications can be associated with the authenticated user and retrieved through the API.

Examples of notification-driven workflows include:

### Instructor Approval

```text
Instructor Application
        ↓
Admin Decision
        ↓
Notification
        ↓
Instructor
```

### Course / Learning Events

Relevant learning-related events can notify users when important actions occur.

The notification architecture keeps user-facing updates separate from the core business logic.

---


# 🛡️ Authorization & Middleware

Role-based middleware is used to protect routes according to the authenticated user's role.

Examples:

```text
Admin
Instructor
Student
```

Authorization rules ensure that users can only perform actions appropriate to their role and relationship with the resource.

Examples include:

* Only approved instructors can perform instructor-specific course operations.
* Students create their own enrollments.
* Instructors manage their own courses and lessons.
* Students access learning resources according to their enrollment.
* Administrators have platform-level management capabilities.

---

# 📄 API Resources

Laravel API Resources are used to transform database models into structured JSON responses.

This keeps the API response format consistent and prevents directly exposing database models.

Resources are used across major modules such as:

* Users
* Courses
* Lessons
* Enrollments
* Notifications

---

# ✅ Form Requests & Validation

Dedicated Laravel Form Request classes are used to validate incoming requests.

Validation is separated from controllers to keep the application's request handling clean and maintainable.

Validation covers areas such as:

* User registration.
* Profile updates.
* Course creation.
* Lesson creation.
* Enrollment requests.
* Uploaded files.

Invalid requests return appropriate Laravel validation responses.

---

# 📤 File Uploads

The API supports file uploads for learning resources and user content.

Supported use cases include:

* Profile images.
* Course-related media.
* Lesson images.
* Lesson videos.

Laravel Storage is used to manage uploaded files.

---

# 📑 Pagination

Pagination is implemented for collections that may contain large numbers of records.

Examples include:

* Courses.
* Lessons.
* Enrollments.
* Other collection-based endpoints.

This prevents unnecessarily large API responses and makes the API more suitable for frontend consumption.

---

# 🔗 Database Relationships

The application uses Eloquent relationships to model the learning platform.

### User Relationships

```text
User
 ├── Profile
 ├── Courses
 ├── Enrollments
 └── Notifications
```

### Course Relationships

```text
Course
 ├── Instructor
 ├── Category
 ├── Lessons
 └── Enrollments
```

### Enrollment Relationships

```text
User
   ↕
Enrollment
   ↕
Course
```

These relationships allow the API to retrieve and authorize learning data according to the user's role and ownership.

---

# 📂 Core API Modules

| Module             | Description                                                |
| :----------------- | :--------------------------------------------------------- |
| **Authentication** | Registration, login, logout, and authenticated user access |
| **Profiles**       | Profile information, phone, and image management           |
| **Users**          | Role-based user management                                 |
| **Categories**     | Course categorization                                      |
| **Courses**        | Course creation and management                             |
| **Lessons**        | Course lesson and media management                         |
| **Enrollments**    | Student enrollment and enrollment management               |
| **Notifications**  | User notification retrieval and event-driven updates       |

---

# 🧪 API Testing

The API can be manually tested using the provided **Postman Collection**.

The collection can be used to test:

* Authentication.
* Role-based endpoints.
* Instructor approval workflow.
* Course management.
* Lesson management.
* Enrollment.
* Authorization scenarios.
* File uploads.
* Notifications.

---

# 🚀 Getting Started

## 1. Clone the Repository

```bash
git clone https://github.com/your-username/your-repository.git
cd your-repository
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

Configure the database and other required environment variables inside `.env`.

## 4. Run Migrations

```bash
php artisan migrate
```

## 5. Create Storage Link

```bash
php artisan storage:link
```

## 6. Start the Application

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000
```

---

# 🧰 Technologies

* **PHP 8.2+**
* **Laravel 11**
* **MySQL**
* **Laravel Sanctum**
* **Eloquent ORM**
* **Laravel Middleware**
* **Laravel API Resources**
* **Laravel Form Requests**
* **Laravel Notifications**
* **Laravel Events / Listeners**
* **Laravel Queues**
* **Laravel Storage**
* **Postman**

---

# 📌 What This Project Demonstrates

This project demonstrates practical backend development through a complete learning-platform workflow rather than simple CRUD operations.

Key concepts include:

* RESTful API development.
* Authentication with Laravel Sanctum.
* Role-based authorization.
* Admin / Instructor / Student workflows.
* Instructor approval system.
* Course management.
* Lesson management.
* Student enrollment.
* Duplicate enrollment prevention.
* Enrollment-based access control.
* Instructor ownership authorization.
* File and media uploads.
* Pagination.
* API Resources.
* Form Requests.
* Eloquent relationships.
* Notifications.
* Event-driven workflows.
* Queued background processing.
* Middleware-based authorization.

---

## 🚧 Future Improvements

Potential extensions include:

* Automated Feature / Integration Tests.
* Advanced course search and filtering.
* Course sorting.
* Course reviews and ratings.
* Progress tracking.
* Lesson completion tracking.
* Certificates.
* Payment integration.
* Redis-based production queues.
* Dockerized development environment.
* CI/CD pipeline.
* Production deployment.
