# API Documentation

This PHP backend serves as a REST API for your React frontend. All endpoints return JSON responses.

## Base URL
```
http://localhost/easil_backend/api/
```

## Endpoints

### 1. Register User
**POST** `/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "username": "johndoe",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "username": "johndoe",
    "name": "John Doe"
  }
}
```

### 2. Login
**POST** `/login`

**Request Body:**
```json
{
  "username": "johndoe",
  "password": "password123",
  "remember": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "id": 1,
    "username": "johndoe",
    "name": "John Doe",
    "created_at": "2024-01-01 12:00:00",
    "isLoggedIn": true
  }
}
```

### 3. Get Profile
**GET** `/profile`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "username": "johndoe",
    "name": "John Doe",
    "created_at": "2024-01-01 12:00:00",
    "isLoggedIn": true
  }
}
```

### 4. Update Profile
**PUT** `/update`

**Request Body:**
```json
{
  "name": "John Smith"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "username": "johndoe",
    "name": "John Smith",
    "created_at": "2024-01-01 12:00:00"
  }
}
```

### 5. Change Password
**POST** `/change-password`

**Request Body:**
```json
{
  "current_password": "oldpassword",
  "new_password": "newpassword123",
  "confirm_password": "newpassword123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

### 6. Logout
**POST** `/logout`

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

## Error Responses

All endpoints return error responses in this format:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": "Validation error message"
  }
}
```

## React Frontend Integration

Here's how to integrate with your React frontend:

### Example API Service (React)

```javascript
// api.js
const API_BASE_URL = 'http://localhost/easil_backend/api';

export const api = {
  async register(userData) {
    const response = await fetch(`${API_BASE_URL}/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(userData),
    });
    return response.json();
  },

  async login(credentials) {
    const response = await fetch(`${API_BASE_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(credentials),
    });
    return response.json();
  },

  async getProfile() {
    const response = await fetch(`${API_BASE_URL}/profile`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    return response.json();
  },

  async updateProfile(profileData) {
    const response = await fetch(`${API_BASE_URL}/update`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(profileData),
    });
    return response.json();
  },

  async changePassword(passwordData) {
    const response = await fetch(`${API_BASE_URL}/change-password`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(passwordData),
    });
    return response.json();
  },

  async logout() {
    const response = await fetch(`${API_BASE_URL}/logout`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    return response.json();
  },
};
```

### Example React Component Usage

```javascript
import React, { useState } from 'react';
import { api } from './api';

function Login() {
  const [credentials, setCredentials] = useState({
    username: '',
    password: '',
    remember: false
  });

  const handleLogin = async (e) => {
    e.preventDefault();
    try {
      const response = await api.login(credentials);
      if (response.success) {
        console.log('Login successful:', response.data);
      } else {
        console.error('Login failed:', response.message);
      }
    } catch (error) {
      console.error('API error:', error);
    }
  };

  return (
    <form onSubmit={handleLogin}>
      <input
        type="text"
        placeholder="Username"
        value={credentials.username}
        onChange={(e) => setCredentials({...credentials, username: e.target.value})}
      />
      <input
        type="password"
        placeholder="Password"
        value={credentials.password}
        onChange={(e) => setCredentials({...credentials, password: e.target.value})}
      />
      <button type="submit">Login</button>
    </form>
  );
}
```

## CORS Configuration

The API is configured to allow cross-origin requests from your React frontend. Make sure your React app is running on a different port (e.g., `http://localhost:3000`) and the API is accessible at `http://localhost/easil_backend/api/`.

## Database Setup

Make sure your MySQL database is set up with the required tables. The database configuration is in `core/init.php`. 