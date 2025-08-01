Easil Examination Management Platform
Project Overview
This document outlines the core functionalities for a comprehensive edtech platform designed to manage examinations.

## System Architecture

### Technology Stack
- **Backend**: PHP with custom MVC framework
- **Frontend**: React.js with Tailwind CSS
- **Database**: MySQL/MariaDB
- **AI Integration**: LLM for assessment creation and grading
- **Authentication**: Role-based access control (RBAC)

###  Features

### Student Features
- **Assessment Dashboard**: View all assessments with status indicators
- **Assessment Taking**: Interactive interface for theory-based assessments
- **Result Tracking**: Real-time access to graded results
- **Progress Monitoring**: Track performance across all courses

### Lecturer Features
- **Assessment Creation**: 
  - Manual assessment creation
  - LLM-assisted assessment generation
  - Question bank management
- **Assessment Management**:
  - Modify existing assessments
  - Delete assessments
  - Set assessment schedules
- **Student Management**:
  - Add/remove students from courses
  - Enroll new students
  - Assign assessments to students
- **Course Management**:
  - Create and delete courses
  - Activate/deactivate courses
  - Course content management
- **Grade Management**:
  - View individual student grades
  - Course-wide grade analytics
  - Performance tracking

### Administrator Features
- **Analytics Dashboard**:
  - Course performance metrics
  - Student enrollment statistics
  - Pass/fail rate analysis
  - Absentee tracking
- **User Management**:
  - Create new lecturers with employment ID-based usernames
  - Add students with matriculation number-based usernames
  - Manage default passwords and force password changes
  - Delete users as needed
- **Course Management**:
  - Add new courses
  - Delete existing courses
  - Course assignment to lecturers
- **System Administration**:
  - User role management
  - System configuration
  - Backup and maintenance

## Authentication & Security

### Login System
- **Role-based Authentication**: Users must specify their role during login
- **Secure Password Management**: Default passwords with forced change on first login
- **Session Management**: Secure session handling with automatic logout
- **Remember Me**: Optional persistent login functionality

### Registration System
- **Administrator Registration**: 
  - First admin created during system deployment
  - Subsequent admins created by existing administrators using registration codes
- **Lecturer Registration**: Created by administrators with employment ID
- **Student Registration**: Created by administrators with matriculation number

### Security Features
- CSRF protection for all forms
- Input validation and sanitization
- Role-based access control
- Secure password hashing
- Session timeout management

## Database Schema

### Core Tables
- `users`: User accounts with role information
- `roles`: Role definitions and permissions
- `courses`: Course information and status
- `assessments`: Assessment details and scheduling
- `enrollments`: Student-course relationships
- `grades`: Assessment results and scores
- `admin_registration_codes`: Secure admin creation codes

### Key Relationships
- Users belong to specific roles (Student, Lecturer, Administrator)
- Students are enrolled in multiple courses
- Lecturers are assigned to specific courses
- Assessments are linked to courses and students

## Installation & Setup

### Prerequisites
- PHP 8.0 or higher
- MySQL/MariaDB 10.4 or higher
- Apache/Nginx web server
- Node.js 16+ (for frontend development)

### Backend Setup
1. Clone the repository
2. Configure database connection in `easil_backend/classes/config.php`
3. Import database schema from `easil_backend/easil.sql`
4. Set up the first administrator account
5. Configure web server to point to `easil_backend/`

### Frontend Setup
1. Navigate to `Easil_frontend/`
2. Install dependencies: `npm install`
3. Start development server: `npm run dev`
4. Build for production: `npm run build`

### AI Integration Setup
1. Configure LLM API credentials
2. Set up assessment generation endpoints
3. Configure AI grading parameters
4. Test integration with sample assessments

## Configuration

### Environment Variables
- Database connection details
- AI API credentials
- Session configuration
- Email settings (if applicable)

### System Settings
- Default password policies
- Session timeout values
- Assessment time limits
- Grade calculation algorithms

## Usage Workflow

### Administrator Workflow
1. **System Setup**: Create initial administrator account
2. **Course Creation**: Add courses to the system
3. **Lecturer Management**: Create lecturer accounts and assign courses
4. **Student Management**: Enroll students and assign courses
5. **Monitoring**: Track system performance and user activity

### Lecturer Workflow
1. **Login**: Access lecturer dashboard
2. **Course Management**: View assigned courses
3. **Assessment Creation**: Create new assessments (manual or AI-assisted)
4. **Student Management**: Manage enrolled students
5. **Grade Review**: Review and analyze student performance

### Student Workflow
1. **Login**: Access student dashboard
2. **Assessment View**: Browse available assessments
3. **Assessment Taking**: Complete theory-based assessments
4. **Result Review**: View AI-graded results
5. **Progress Tracking**: Monitor performance across courses

## AI Integration

### Assessment Creation
- LLM-assisted question generation
- Automatic difficulty adjustment
- Topic-based assessment creation
- Question bank management

### Assessment Grading
- AI-powered theory assessment grading
- Natural language processing for essay questions
- Automated feedback generation
- Grade consistency validation

## API Documentation

### Authentication Endpoints
- `POST /api/login`: User authentication
- `POST /api/logout`: User logout

### Assessment Endpoints
- `GET /api/assessments`: List assessments
- `POST /api/assessments`: Create assessment
- `PUT /api/assessments/{id}`: Update assessment
- `DELETE /api/assessments/{id}`: Delete assessment

### User Management Endpoints
- `GET /api/users`: List users
- `POST /api/users`: Create user
- `PUT /api/users/{id}`: Update user
- `DELETE /api/users/{id}`: Delete user

## Testing

### Test Coverage
- Unit tests for core functionality
- Integration tests for API endpoints
- End-to-end tests for user workflows
- Security testing for authentication

### Test Commands
```bash
# Run backend tests
php vendor/bin/phpunit

# Run frontend tests
npm test

# Run end-to-end tests
npm run test:e2e
```

## Deployment

### Production Setup
1. Configure production database
2. Set up SSL certificates
3. Configure web server (Apache/Nginx)
4. Set environment variables
5. Deploy frontend build
6. Configure AI service endpoints

### Monitoring
- Application performance monitoring
- Database performance tracking
- User activity analytics
- Error logging and alerting

## Contributing

### Development Guidelines
1. Follow coding standards and conventions
2. Write comprehensive tests
3. Update documentation for new features
4. Submit pull requests for review

### Code Style
- PHP: PSR-12 coding standards
- JavaScript: ESLint configuration
- CSS: Tailwind CSS utility classes

## License

This project is licensed under the MIT License - see the LICENSE file for details.

### Planned Features
- Mobile application
- Advanced analytics
- Integration with external LMS
- Enhanced AI capabilities
- Real-time notifications
- Multiple schools management


