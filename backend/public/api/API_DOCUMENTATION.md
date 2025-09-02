# EASIL Platform API Documentation

## Overview
This document describes the RESTful APIs for the EASIL exam management platform. All APIs return JSON responses and use standard HTTP methods.

## Base URL
```
http://easil.ng.com/backend/public/api/
```

## Authentication
Most endpoints require authentication. After login, the session is maintained server-side. Include the session cookie in subsequent requests.

## Response Format
All API responses follow this standard format:
```json
{
  "success": true/false,
  "message": "Optional message",
  "data": {...},
  "error": "Error message if success is false"
}
```

## API Endpoints

### 1. Authentication (`/auth`)

#### Login
- **POST** `/auth/login`
- **Description**: Authenticate user and start session
- **Body**:
```json
{
  "username": "student123",
  "password": "password123"
}
```
- **Response**:
```json
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "username": "student123",
    "name": "John Doe",
    "email": "john@example.com",
    "role_id": 2,
    "role_name": "Student",
    "identification_number": "STU001",
    "force_password_change": false,
    "status": "active"
  },
  "token": "base64_encoded_token"
}
```

#### Logout
- **POST** `/auth/logout`
- **Description**: End user session
- **Response**:
```json
{
  "success": true,
  "message": "Logout successful"
}
```

#### Get Profile
- **GET** `/auth/profile`
- **Description**: Get current user profile
- **Response**:
```json
{
  "success": true,
  "user": {
    "id": 1,
    "username": "student123",
    "name": "John Doe",
    "email": "john@example.com",
    "role_id": 2,
    "role_name": "Student",
    "identification_number": "STU001",
    "status": "active",
    "created_at": "2024-01-01 00:00:00"
  }
}
```

#### Update Profile
- **PUT** `/auth/profile`
- **Description**: Update current user profile
- **Body**:
```json
{
  "name": "John Smith",
  "email": "johnsmith@example.com"
}
```

#### Change Password
- **POST** `/auth/change-password`
- **Description**: Change user password
- **Body**:
```json
{
  "current_password": "oldpassword",
  "new_password": "newpassword123"
}
```

### 2. User Management (`/users`)

#### List Users (Admin Only)
- **GET** `/users?page=1&limit=20&role_id=2&status=active`
- **Query Parameters**:
  - `page`: Page number (default: 1)
  - `limit`: Items per page (default: 20)
  - `role_id`: Filter by role (2=Student, 3=Lecturer, 4=Admin)
  - `status`: Filter by status
  - `keywords`: Search in username, name, email
  - `department`: Filter by department

#### Create User (Admin Only)
- **POST** `/users/create`
- **Body**:
```json
{
  "username": "newstudent",
  "name": "New Student",
  "email": "newstudent@example.com",
  "role_id": 2,
  "identification_number": "STU002",
  "department": "Computer Science"
}
```

#### Update User (Admin Only)
- **PUT** `/users/update`
- **Body**:
```json
{
  "id": 1,
  "name": "Updated Name",
  "email": "updated@example.com",
  "department": "Mathematics"
}
```

#### Delete User (Admin Only)
- **DELETE** `/users/delete`
- **Body**:
```json
{
  "id": 1
}
```

#### Bulk Import Users (Admin Only)
- **POST** `/users/bulk-import`
- **Body**:
```json
{
  "users": [
    {
      "username": "student1",
      "name": "Student One",
      "email": "student1@example.com",
      "role_id": 2,
      "identification_number": "STU003"
    },
    {
      "username": "student2",
      "name": "Student Two",
      "email": "student2@example.com",
      "role_id": 2,
      "identification_number": "STU004"
    }
  ]
}
```

#### Export Users (Admin Only)
- **GET** `/users/export?role_id=2&status=active`
- **Response**: CSV file download

#### Get Roles
- **GET** `/users/roles`
- **Description**: Get available user roles

### 3. Course Management (`/courses`)

#### List Courses
- **GET** `/courses?page=1&limit=20&keywords=programming&department=CS`
- **Query Parameters**:
  - `page`: Page number
  - `limit`: Items per page
  - `keywords`: Search in title, code, description
  - `department`: Filter by department
  - `status`: Filter by status
  - `coordinator_id`: Filter by coordinator

#### Get Course Details
- **GET** `/courses/{course_id}`

#### Create Course (Admin/Lecturer)
- **POST** `/courses/create`
- **Body**:
```json
{
  "title": "Introduction to Programming",
  "code": "CS101",
  "description": "Basic programming concepts",
  "department": "Computer Science",
  "coordinator_user_id": 5
}
```

#### Update Course (Admin/Lecturer)
- **PUT** `/courses/update`
- **Body**:
```json
{
  "id": 1,
  "title": "Updated Course Title",
  "description": "Updated description"
}
```

#### Delete Course (Admin Only)
- **DELETE** `/courses/delete`
- **Body**:
```json
{
  "id": 1
}
```

#### Assign Lecturer (Admin Only)
- **POST** `/courses/assign-lecturer`
- **Body**:
```json
{
  "course_id": 1,
  "lecturer_id": 5
}
```

#### Enroll Students (Admin/Lecturer)
- **POST** `/courses/enroll-students`
- **Body**:
```json
{
  "course_id": 1,
  "student_ids": [1, 2, 3],
  "semester": "Fall 2024"
}
```

#### Get Enrolled Students
- **GET** `/courses/enrolled-students?course_id=1`

#### Get Available Courses
- **GET** `/courses/available?department=CS`

### 4. Exam Management (`/exams`)

#### List Exams
- **GET** `/exams?course_id=1&status=active&type=multiple_choice`
- **Query Parameters**:
  - `course_id`: Filter by course
  - `status`: Filter by status
  - `type`: Filter by exam type
  - `keywords`: Search in title

#### Create Exam (Admin/Lecturer)
- **POST** `/exams/create`
- **Body**:
```json
{
  "title": "Midterm Exam",
  "course_id": 1,
  "description": "Midterm examination",
  "duration": 120,
  "total_marks": 100,
  "type": "multiple_choice",
  "passing_marks": 40
}
```

#### Update Exam (Admin/Lecturer)
- **PUT** `/exams/update`
- **Body**:
```json
{
  "id": 1,
  "title": "Updated Exam Title",
  "duration": 90
}
```

#### Delete Exam (Admin/Lecturer)
- **DELETE** `/exams/delete`
- **Body**:
```json
{
  "id": 1
}
```

#### Get Exam Questions
- **GET** `/exams/questions?exam_id=1`

#### Add Exam Question (Admin/Lecturer)
- **POST** `/exams/questions`
- **Body**:
```json
{
  "exam_id": 1,
  "question": "What is 2 + 2?",
  "type": "multiple_choice",
  "marks": 5,
  "options": ["3", "4", "5", "6"],
  "correct_answer": "4"
}
```

#### Take Exam (Student Only)
- **POST** `/exams/take`
- **Body**:
```json
{
  "exam_id": 1
}
```

#### Submit Exam (Student Only)
- **POST** `/exams/submit`
- **Body**:
```json
{
  "exam_session_id": 123,
  "answers": {
    "1": "4",
    "2": "B",
    "3": "Essay answer text..."
  }
}
```

#### Get Exam Results
- **GET** `/exams/results?exam_id=1`

#### Schedule Exam (Admin/Lecturer)
- **POST** `/exams/schedule`
- **Body**:
```json
{
  "exam_id": 1,
  "scheduled_date": "2024-12-15 10:00:00"
}
```

### 5. Enrollments (`/enrollments`)

#### List Enrollments
- **GET** `/enrollments?course_id=1&student_id=1&status=active`
- **Query Parameters**:
  - `course_id`: Filter by course
  - `student_id`: Filter by student
  - `status`: Filter by status
  - `semester`: Filter by semester

#### Create Enrollment (Admin/Lecturer)
- **POST** `/enrollments/create`
- **Body**:
```json
{
  "course_id": 1,
  "student_id": 1,
  "semester": "Fall 2024",
  "academic_year": "2024-2025"
}
```

#### Bulk Enroll (Admin/Lecturer)
- **POST** `/enrollments/bulk-enroll`
- **Body**:
```json
{
  "course_id": 1,
  "student_ids": [1, 2, 3, 4],
  "semester": "Fall 2024"
}
```

#### Remove Enrollment (Admin/Lecturer)
- **DELETE** `/enrollments/remove`
- **Body**:
```json
{
  "course_id": 1,
  "student_id": 1
}
```

#### Get My Courses (Student Only)
- **GET** `/enrollments/my-courses`

#### Get Course Students (Admin/Lecturer)
- **GET** `/enrollments/course-students?course_id=1`

#### Get Enrollment Stats
- **GET** `/enrollments/stats?course_id=1&semester=Fall 2024`

### 6. Results (`/results`)

#### List Results
- **GET** `/results?exam_id=1&student_id=1&course_id=1`
- **Query Parameters**:
  - `exam_id`: Filter by exam
  - `student_id`: Filter by student
  - `course_id`: Filter by course
  - `status`: Filter by status

#### Get My Results (Student Only)
- **GET** `/results/my-results`

#### Get Course Results (Admin/Lecturer)
- **GET** `/results/course-results?course_id=1`

#### Get Exam Results
- **GET** `/results/exam-results?exam_id=1`

#### Grade Exam (Admin/Lecturer)
- **POST** `/results/grade`
- **Body**:
```json
{
  "exam_id": 1,
  "student_id": 1,
  "score": 85,
  "comments": "Good work!",
  "grade": "A"
}
```

#### Bulk Grade (Admin/Lecturer)
- **POST** `/results/bulk-grade`
- **Body**:
```json
{
  "exam_id": 1,
  "grades": [
    {
      "student_id": 1,
      "score": 85,
      "grade": "A"
    },
    {
      "student_id": 2,
      "score": 92,
      "grade": "A+"
    }
  ]
}
```

#### Get Analytics
- **GET** `/results/analytics?course_id=1&semester=Fall 2024`

#### Export Results (Admin/Lecturer)
- **GET** `/results/export?exam_id=1`

#### Publish Results (Admin/Lecturer)
- **POST** `/results/publish`
- **Body**:
```json
{
  "exam_id": 1
}
```

### 7. Dashboard (`/dashboard`)

#### Get General Dashboard
- **GET** `/dashboard`

#### Get Admin Dashboard (Admin Only)
- **GET** `/dashboard/admin`

#### Get Lecturer Dashboard (Lecturer Only)
- **GET** `/dashboard/lecturer`

#### Get Student Dashboard (Student Only)
- **GET** `/dashboard/student`

#### Get Statistics
- **GET** `/dashboard/stats?period=month&course_id=1`

#### Get Recent Activity
- **GET** `/dashboard/recent-activity?limit=10&type=all`

## Error Handling

### HTTP Status Codes
- `200`: Success
- `400`: Bad Request (validation errors)
- `401`: Unauthorized (authentication required)
- `403`: Forbidden (insufficient permissions)
- `404`: Not Found
- `405`: Method Not Allowed
- `500`: Internal Server Error

### Error Response Format
```json
{
  "success": false,
  "error": "Error description",
  "details": "Additional error details if available"
}
```

## Pagination

Most list endpoints support pagination with these query parameters:
- `page`: Page number (starts from 1)
- `limit`: Items per page (default: 20)

Response includes pagination metadata:
```json
{
  "success": true,
  "data": {
    "items": [...],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 150,
      "pages": 8
    }
  }
}
```

## React Frontend Integration

### Example: Login Component
```jsx
import React, { useState } from 'react';

const Login = () => {
  const [credentials, setCredentials] = useState({
    username: '',
    password: ''
  });

  const handleLogin = async (e) => {
    e.preventDefault();
    
    try {
      const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials),
        credentials: 'include' // Important for session cookies
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Store user data in context/state
        localStorage.setItem('user', JSON.stringify(data.user));
        // Redirect based on role
        if (data.user.role_id === 1) {
          window.location.href = '/admin/dashboard';
        } else if (data.user.role_id === 2) {
          window.location.href = '/student/dashboard';
        } else if (data.user.role_id === 3) {
          window.location.href = '/lecturer/dashboard';
        }
      } else {
        alert(data.error);
      }
    } catch (error) {
      console.error('Login error:', error);
      alert('Login failed. Please try again.');
    }
  };

  return (
    <form onSubmit={handleLogin}>
      <input
        type="text"
        placeholder="Username"
        value={credentials.username}
        onChange={(e) => setCredentials({
          ...credentials,
          username: e.target.value
        })}
      />
      <input
        type="password"
        placeholder="Password"
        value={credentials.password}
        onChange={(e) => setCredentials({
          ...credentials,
          password: e.target.value
        })}
      />
      <button type="submit">Login</button>
    </form>
  );
};

export default Login;
```

### Example: API Service
```javascript
// apiService.js
const API_BASE = '/api';

class ApiService {
  constructor() {
    this.baseUrl = API_BASE;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    
    const config = {
      credentials: 'include', // Include cookies
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.error || 'Request failed');
      }
      
      return data;
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  }

  // Auth methods
  async login(credentials) {
    return this.request('/auth/login', {
      method: 'POST',
      body: JSON.stringify(credentials),
    });
  }

  async logout() {
    return this.request('/auth/logout', {
      method: 'POST',
    });
  }

  async getProfile() {
    return this.request('/auth/profile');
  }

  // User methods
  async getUsers(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    return this.request(`/users?${queryString}`);
  }

  async createUser(userData) {
    return this.request('/users/create', {
      method: 'POST',
      body: JSON.stringify(userData),
    });
  }

  // Course methods
  async getCourses(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    return this.request(`/courses?${queryString}`);
  }

  async createCourse(courseData) {
    return this.request('/courses/create', {
      method: 'POST',
      body: JSON.stringify(courseData),
    });
  }

  // Exam methods
  async getExams(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    return this.request(`/exams?${queryString}`);
  }

  async createExam(examData) {
    return this.request('/exams/create', {
      method: 'POST',
      body: JSON.stringify(examData),
    });
  }

  // Dashboard methods
  async getDashboard(type = '') {
    const endpoint = type ? `/dashboard/${type}` : '/dashboard';
    return this.request(endpoint);
  }
}

export default new ApiService();
```

### Example: Using API Service in Components
```jsx
import React, { useState, useEffect } from 'react';
import apiService from '../services/apiService';

const CourseList = () => {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    loadCourses();
  }, []);

  const loadCourses = async () => {
    try {
      setLoading(true);
      const response = await apiService.getCourses({
        page: 1,
        limit: 20,
        status: 'active'
      });
      
      if (response.success) {
        setCourses(response.data.courses);
      }
    } catch (error) {
      setError(error.message);
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div>Loading courses...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div>
      <h2>Courses</h2>
      {courses.map(course => (
        <div key={course.id}>
          <h3>{course.title}</h3>
          <p>{course.description}</p>
          <p>Code: {course.code}</p>
        </div>
      ))}
    </div>
  );
};

export default CourseList;
```

## Testing the APIs

You can test these APIs using tools like:
- **Postman**: For manual API testing
- **cURL**: For command-line testing
- **Thunder Client**: VS Code extension for API testing

### Example cURL Commands

#### Login
```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}' \
  -c cookies.txt
```

#### Get Courses (with session cookie)
```bash
curl -X GET http://localhost/api/courses \
  -b cookies.txt
```

#### Create Course
```bash
curl -X POST http://localhost/api/courses/create \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{"title":"Test Course","code":"TEST101","description":"Test description"}'
```

## Security Considerations

1. **Session Management**: Uses server-side sessions with secure cookies
2. **Role-Based Access Control**: Each endpoint checks user permissions
3. **Input Validation**: All inputs are validated and sanitized
4. **SQL Injection Protection**: Uses prepared statements
5. **CORS**: Configured for React frontend integration

## Rate Limiting

Consider implementing rate limiting for production use:
- Login attempts: 5 per minute per IP
- API calls: 100 per minute per user
- File uploads: 10 per hour per user

## Monitoring and Logging

The API includes:
- Error logging for debugging
- Audit trails for sensitive operations
- Performance monitoring hooks
- User activity tracking

## Support

For API support or questions:
1. Check the error responses for specific error messages
2. Verify authentication and permissions
3. Check the server logs for detailed error information
4. Ensure all required fields are provided in requests
