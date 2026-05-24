# School Management ERP System

## Overview
A comprehensive, professional-grade School Management ERP system with role-based access control, modern responsive design, and advanced features for efficient educational institution management.

## Features

### Multi-Module System
- **Admin Dashboard**: Complete institutional management
- **Teacher Portal**: Student management and assessment
- **Student Portal**: Academic tracking and notifications
- **Parent Portal**: Child's academic monitoring

### Core Modules
1. **Authentication & Authorization**
   - Secure login with role-based access
   - Password encryption (bcrypt/Argon2)
   - Session management with timeouts
   - CSRF protection

2. **Admin Dashboard**
   - Student admission management
   - Staff management
   - Class and section management
   - Attendance tracking
   - Fee management
   - Timetable scheduling
   - Examination management
   - Subject allocation
   - Library management
   - Transport management
   - Event notifications
   - Report generation
   - Database control

3. **Teacher Dashboard**
   - Student attendance management
   - Mark uploads
   - Assignment management
   - Study material uploads
   - Exam schedule view
   - Student communication

4. **Student Dashboard**
   - Personal profile
   - Attendance percentage
   - Marks and results
   - Assignment tracking
   - Class timetable
   - Notifications
   - Events calendar
   - Fee status
   - Study materials download

5. **Parent Dashboard**
   - Child attendance monitoring
   - Academic performance tracking
   - Fee details
   - School announcements

### Technical Features
- **Security**: Password encryption, prepared statements, CSRF tokens, XSS prevention
- **Responsive Design**: Mobile-first, works on all devices
- **UI/UX**: Modern cards, animations, charts, dark/light mode
- **Reports**: PDF export, printable reports
- **Notifications**: Email notifications
- **Search & Filter**: Advanced filtering capabilities
- **File Upload**: Profile images, study materials
- **Data Management**: Comprehensive CRUD operations

## Tech Stack
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Additional**: Chart.js, DataTables, Moment.js, SweetAlert2

## Project Structure
```
school-management-erp/
├── index.php                 # Homepage
├── config.php               # Database configuration
├── functions.php            # Helper functions
├── authentication.php       # Login & session handling
├── login.php               # Login page
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   ├── dashboard.css
│   │   ├── responsive.css
│   │   └── dark-mode.css
│   ├── js/
│   │   ├── main.js
│   │   ├── dashboard.js
│   │   ├── charts.js
│   │   └── notifications.js
│   └── images/
│       ├── logo.png
│       ├── banner.jpg
│       └── icons/
│
├── admin/
│   ├── dashboard.php
│   ├── students.php
│   ├── staff.php
│   ├── classes.php
│   ├── attendance.php
│   ├── fees.php
│   ├── timetable.php
│   ├── exams.php
│   ├── library.php
│   ├── transport.php
│   ├── events.php
│   ├── reports.php
│   └── settings.php
│
├── teacher/
│   ├── dashboard.php
│   ├── attendance.php
│   ├── marks.php
│   ├── assignments.php
│   ├── materials.php
│   └── students.php
│
├── student/
│   ├── dashboard.php
│   ├── profile.php
│   ├── attendance.php
│   ├── marks.php
│   ├── assignments.php
│   ├── timetable.php
│   ├── materials.php
│   └── fees.php
│
├── parent/
│   ├── dashboard.php
│   ├── child_progress.php
│   ├── attendance.php
│   ├── fees.php
│   └── announcements.php
│
├── database/
│   ├── schema.sql
│   └── seed_data.sql
│
├── api/
│   ├── attendance.php
│   ├── marks.php
│   ├── assignments.php
│   └── notifications.php
│
└── uploads/
    ├── profiles/
    ├── materials/
    └── documents/
```

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (optional, for advanced features)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/MAHES162005/school-management-erp.git
   cd school-management-erp
   ```

2. **Setup Database**
   - Create a MySQL database named `school_erp`
   - Import `database/schema.sql`
   ```bash
   mysql -u root -p school_erp < database/schema.sql
   ```

3. **Configure Database Connection**
   - Update `config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASSWORD', '');
   define('DB_NAME', 'school_erp');
   ```

4. **Setup Web Server**
   - Place files in web root (htdocs for XAMPP, www for WAMP)
   - Start Apache and MySQL services
   - Access via `http://localhost/school-management-erp`

5. **Create Upload Directories**
   ```bash
   mkdir -p uploads/profiles
   mkdir -p uploads/materials
   mkdir -p uploads/documents
   chmod 755 uploads/*
   ```

6. **Access Application**
   - Homepage: `http://localhost/school-management-erp`
   - Login: `http://localhost/school-management-erp/login.php`

## Default Login Credentials

⚠️ **IMPORTANT**: Change these credentials immediately after first login!

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@school.com | admin@123 |
| Teacher | teacher@school.com | pass@123 |
| Student | student@school.com | pass@123 |
| Parent | parent@school.com | pass@123 |

## Security Features

✅ **Password Security**
- Bcrypt/Argon2 hashing
- Secure password storage
- Password reset functionality

✅ **Session Management**
- 30-minute inactivity timeout
- Session regeneration after login
- Secure cookie settings

✅ **SQL Security**
- Prepared statements (no SQL injection)
- Input validation and sanitization
- Output escaping (XSS prevention)

✅ **CSRF Protection**
- CSRF tokens on all forms
- Token validation on submission

✅ **Rate Limiting**
- Login attempt throttling
- Account lockout after 5 failed attempts

✅ **Access Control**
- Role-based access control (RBAC)
- Per-route authentication checks

## Features in Detail

### 1. Authentication System
- **Secure Login**: Email and password with bcrypt hashing
- **Password Recovery**: Email-based password reset
- **Session Management**: Timeout and secure cookies
- **Remember Me**: Optional persistent login
- **Account Lockout**: Protection against brute force
- **Activity Logging**: Tracks all user actions

### 2. Admin Features
- **Student Management**
  - Add/edit student details
  - Upload admission documents
  - Manage enrollments
  - Student search and filtering

- **Staff Management**
  - Add/edit teacher information
  - Assign subjects and classes
  - Track qualifications

- **Class Management**
  - Create classes and sections
  - Assign class teachers
  - Manage section capacity

- **Attendance Tracking**
  - Mark daily attendance
  - Generate attendance reports
  - Monitor attendance percentage

- **Fee Management**
  - Define fee structures
  - Track fee payments
  - Generate fee reports
  - Send payment reminders

- **Timetable Scheduling**
  - Create weekly timetables
  - Manage class timings
  - Assign rooms and teachers

- **Examination Management**
  - Create exams
  - Schedule exam dates
  - Manage exam results
  - Generate scorecards

- **Subject Allocation**
  - Assign subjects to classes
  - Allocate teachers to subjects
  - Manage subject credits

- **Library Management**
  - Add/remove books
  - Track book issuance
  - Manage book inventory
  - Calculate late fees

- **Transport Management**
  - Manage transport routes
  - Track student assignments
  - Monitor vehicle details

- **Event Management**
  - Create school events
  - Send event notifications
  - Track event attendance

- **Reports & Analytics**
  - Generate attendance reports
  - Performance analytics
  - Fee collection reports
  - PDF export functionality

### 3. Teacher Features
- **Attendance Marking**
  - Daily attendance entry
  - Bulk upload from CSV
  - Mark regularization requests

- **Marks Management**
  - Upload student marks
  - Create gradebooks
  - Generate report cards
  - Track progress

- **Assignment Management**
  - Create assignments
  - Set due dates
  - Review submissions
  - Grade assignments

- **Study Materials**
  - Upload class notes
  - Share PDFs and documents
  - Manage study resources
  - Track downloads

- **Student Communication**
  - Send notifications
  - Manage announcements
  - Communicate with parents

### 4. Student Features
- **Profile Management**
  - View personal information
  - Upload profile photo
  - Update contact details

- **Attendance Tracking**
  - View attendance percentage
  - Monthly attendance report
  - Leave applications

- **Academic Performance**
  - View marks and grades
  - Download scorecards
  - Track subject progress

- **Assignment Portal**
  - View assignments
  - Submit assignments
  - Track submission status
  - Receive feedback

- **Timetable**
  - View class schedule
  - Download timetable
  - Calendar view

- **Study Materials**
  - Access class notes
  - Download resources
  - Search materials

- **Notifications**
  - Receive announcements
  - Event notifications
  - Assignment reminders
  - Grade notifications

- **Fee Status**
  - View fee details
  - Download fee receipts
  - Payment history

### 5. Parent Features
- **Child Monitoring**
  - Link to child's account
  - View academic progress
  - Track attendance

- **Academic Reports**
  - View marks and grades
  - Download scorecards
  - Performance analysis

- **Attendance Monitoring**
  - View child's attendance
  - Attendance reports
  - Alert on absences

- **Fee Management**
  - View fee status
  - Download receipts
  - Payment tracking

- **School Announcements**
  - Receive notifications
  - View school events
  - Important updates

- **Communication**
  - Message teachers
   - Receive alerts

## API Endpoints

All API endpoints are located in `/api/` and require authentication.

### Attendance API
```
GET  /api/attendance.php?action=get&student_id=1
GET  /api/attendance.php?action=summary&student_id=1
POST /api/attendance.php?action=mark
POST /api/attendance.php?action=bulk_upload
```

### Marks API
```
GET  /api/marks.php?action=get&student_id=1
GET  /api/marks.php?action=list&class_id=1
POST /api/marks.php?action=upload
POST /api/marks.php?action=bulk_upload
```

### Assignments API
```
GET  /api/assignments.php?action=list
GET  /api/assignments.php?action=get&assignment_id=1
POST /api/assignments.php?action=create
POST /api/assignments.php?action=submit
```

### Notifications API
```
GET  /api/notifications.php?action=list
GET  /api/notifications.php?action=unread
POST /api/notifications.php?action=send
POST /api/notifications.php?action=mark_read
```

## Customization

### Theme Customization
1. Edit `assets/css/style.css` to modify colors
2. Update Bootstrap variables in CSS files
3. Upload custom school logo in admin settings
4. Customize email templates

### Adding New Modules
1. Create module folder under appropriate role directory
2. Design database tables in schema
3. Create PHP files for CRUD operations
4. Create API endpoints in `/api/`
5. Update navigation menu
6. Add permissions to access control

## Performance Optimization

- Database indexing for fast queries
- CSS/JS minification
- Image optimization
- Query caching
- Session management

## Browser Support

✅ Chrome (Latest)
✅ Firefox (Latest)
✅ Safari (Latest)
✅ Edge (Latest)
✅ Mobile Browsers

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `config.php`
- Ensure database `school_erp` exists

### Upload Folder Permissions
```bash
chmod 755 uploads/*
chown www-data:www-data uploads/
```

### Session Issues
- Clear browser cookies
- Check PHP session settings
- Verify temp directory permissions

### Email Not Sending
- Configure SMTP in `config.php`
- Check server's mail settings
- Verify email credentials

## Support & Documentation

For detailed documentation:
- Installation Guide: `docs/INSTALLATION.md`
- User Guide: `docs/USER_GUIDE.md`
- API Reference: `docs/API_REFERENCE.md`
- Developer Guide: `docs/DEVELOPER_GUIDE.md`

## License

MIT License - See LICENSE.md for details

## Contributors

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## Support

For support and queries:
- Email: support@schoolerp.com
- Website: https://schoolerp.com
- Issues: https://github.com/MAHES162005/school-management-erp/issues

---

**Last Updated**: May 2024
**Version**: 1.0.0
**Author**: School ERP Team