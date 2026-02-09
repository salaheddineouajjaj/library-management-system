# Software Requirements Specification (SRS)

## Library Management System with AI Recommendations

**Version:** 1.0  
**Date:** February 2026  
**Prepared by:** [Votre Nom]  
**Organization:** [Nom de l'établissement]

---

## Table of Contents

1. [Introduction](#1-introduction)
   - 1.1 Purpose
   - 1.2 Document Conventions
   - 1.3 Intended Audience
   - 1.4 Project Scope
   - 1.5 References
2. [Overall Description](#2-overall-description)
   - 2.1 Product Perspective
   - 2.2 Product Functions
   - 2.3 User Classes and Characteristics
   - 2.4 Operating Environment
   - 2.5 Design and Implementation Constraints
   - 2.6 Assumptions and Dependencies
3. [System Features](#3-system-features)
4. [External Interface Requirements](#4-external-interface-requirements)
5. [Non-Functional Requirements](#5-non-functional-requirements)
6. [Other Requirements](#6-other-requirements)
7. [Appendix](#7-appendix)

---

## 1. Introduction

### 1.1 Purpose

This Software Requirements Specification (SRS) document provides a complete description of the Library Management System with AI-powered recommendations. It describes the functional and non-functional requirements for version 1.0 of the system.

The document is intended for:
- Development team members
- Project managers
- Quality assurance team
- System administrators
- End users (librarians and library members)

### 1.2 Document Conventions

**Conventions used:**
- **Shall/Must** indicates mandatory requirements
- **Should** indicates desirable requirements
- **May** indicates optional requirements
- **FR** = Functional Requirement
- **NFR** = Non-Functional Requirement

**Priority Levels:**
- **High** - Critical for system operation
- **Medium** - Important but not critical
- **Low** - Nice to have features

### 1.3 Intended Audience

This document is intended for:

**Developers:** Complete functional and technical specifications  
**Testers:** Requirements for test case development  
**Project Managers:** Project planning and tracking  
**End Users:** Understanding system capabilities  
**Stakeholders:** System overview and value proposition

### 1.4 Project Scope

The Library Management System is a web-based application designed to:

**Primary Goals:**
- Digitalize library operations (borrowing, returning, cataloging)
- Provide 24/7 access to library services
- Offer personalized book recommendations using AI
- Improve user experience and satisfaction
- Generate real-time statistics and reports

**Key Benefits:**
- Reduce administrative workload (3-4 hours/day saved)
- Increase user autonomy and accessibility
- Enable data-driven decision making
- Enhance book discovery through AI

**Out of Scope:**
- Physical book tracking (RFID/Barcode scanners)
- E-book reader integration
- Payment processing for fines
- Multi-library network system

### 1.5 References

- IEEE Std 830-1998 - IEEE Recommended Practice for SRS
- PHP 8.1 Documentation: https://www.php.net/docs.php
- MySQL 8.0 Reference Manual: https://dev.mysql.com/doc/
- Google Gemini AI API Documentation: https://ai.google.dev/
- OpenLibrary API Documentation: https://openlibrary.org/developers/api

---

## 2. Overall Description

### 2.1 Product Perspective

The Library Management System is a standalone web application that replaces manual library management processes. It consists of:

**System Components:**
1. **Web Frontend** - User interface accessible via browsers
2. **Application Server** - PHP-based business logic (MVC architecture)
3. **Database Server** - MySQL relational database
4. **External APIs** - OpenLibrary and Google Gemini integration

**System Interfaces:**
- **User Interface:** Web browser (Chrome, Firefox, Safari, Edge)
- **Database Interface:** MySQL via PDO
- **External APIs:** REST APIs (JSON)

### 2.2 Product Functions

The system provides the following main functions:

**For All Users:**
- Browse and search book catalog
- View book details and availability

**For Registered Users:**
- Borrow and return books online
- Manage personal reading history
- Create and organize personal bookshelves
- Receive AI-powered book recommendations
- Participate in discussion forums
- Send and receive internal messages

**For Administrators:**
- Manage book catalog (CRUD operations)
- Import book data from OpenLibrary API
- Manage user accounts
- Monitor borrowing activities
- Track overdue books
- Generate statistical reports
- Moderate forum content

### 2.3 User Classes and Characteristics

**Guest (Public User)**
- **Characteristics:** Not authenticated, casual visitor
- **Technical Expertise:** Basic web navigation skills
- **Privileges:** Browse catalog, view book details, register, login
- **Frequency of Use:** Occasional

**Registered User (Library Member)**
- **Characteristics:** Authenticated library member
- **Technical Expertise:** Basic to intermediate computer skills
- **Privileges:** All guest privileges + borrowing, personal shelves, recommendations, forum
- **Frequency of Use:** Regular (weekly)

**Administrator (Librarian)**
- **Characteristics:** Library staff member
- **Technical Expertise:** Intermediate to advanced computer skills
- **Privileges:** All user privileges + catalog management, user management, reports
- **Frequency of Use:** Daily

### 2.4 Operating Environment

**Client-Side Requirements:**
- **Hardware:** Any device with web browser (PC, tablet, smartphone)
- **Software:** Modern web browser (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)
- **Network:** Internet connection (minimum 1 Mbps)

**Server-Side Requirements:**
- **Operating System:** Linux (Ubuntu 22.04 LTS) or Windows Server
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **Application Server:** PHP 8.1+
- **Database Server:** MySQL 8.0+ or MariaDB 10.6+
- **Min Hardware:** 2 CPU cores, 4GB RAM, 50GB SSD
- **Network:** Static IP, HTTPS support

### 2.5 Design and Implementation Constraints

**Technical Constraints:**
- Must use PHP for backend development
- Must use MySQL for data storage
- Must follow MVC architecture pattern
- Must support responsive design (mobile-first)
- Session timeout: 30 minutes of inactivity

**Security Constraints:**
- Passwords must be hashed using bcrypt (cost factor 12)
- All database queries must use prepared statements
- HTTPS must be enforced in production
- User input must be validated and sanitized

**Regulatory Constraints:**
- Must comply with data protection regulations (GDPR)
- User data must be stored securely
- Users must be able to delete their accounts

**Business Constraints:**
- Development budget: [specify if applicable]
- Development timeline: 12 weeks (6 sprints)
- Must use free/open-source technologies

### 2.6 Assumptions and Dependencies

**Assumptions:**
- Users have basic internet literacy
- Users have access to email for notifications
- Library has a reliable internet connection
- Book catalog data is available or can be imported

**Dependencies:**
- **OpenLibrary API** availability for book data enrichment
- **Google Gemini AI API** availability for recommendations
- **Third-party libraries:** None critical (vanilla PHP/JS)
- **Browser compatibility:** Modern browsers with JavaScript enabled

---

## 3. System Features

### 3.1 User Authentication and Authorization

**Priority:** High  
**Description:** System must provide secure user authentication and role-based access control.

#### 3.1.1 User Registration

**FR-1.1:** The system shall allow new users to register with:
- Full name (required, 3-100 characters)
- Email address (required, unique, valid format)
- Password (required, minimum 6 characters)
- Password confirmation (must match)

**FR-1.2:** The system shall validate email uniqueness before registration.

**FR-1.3:** The system shall hash passwords using bcrypt with cost factor 12.

**FR-1.4:** The system shall display appropriate error messages for validation failures.

**FR-1.5:** The system shall redirect users to login page after successful registration.

#### 3.1.2 User Login

**FR-1.6:** The system shall allow users to login with email and password.

**FR-1.7:** The system shall verify credentials against stored hash.

**FR-1.8:** The system shall create a secure session upon successful login.

**FR-1.9:** The system shall redirect users based on their role:
- Regular users → User Dashboard
- Administrators → Admin Dashboard

**FR-1.10:** The system shall display error message for invalid credentials.

**FR-1.11:** The system shall implement protection against brute-force attacks.

#### 3.1.3 User Logout

**FR-1.12:** The system shall provide logout functionality on all pages.

**FR-1.13:** The system shall destroy user session on logout.

**FR-1.14:** The system shall redirect to login page after logout.

#### 3.1.4 Profile Management

**FR-1.15:** Users shall be able to update their profile information:
- Name
- Email (with uniqueness validation)
- Password (with confirmation)

**FR-1.16:** The system shall validate all profile updates.

**FR-1.17:** The system shall display confirmation message after successful update.

---

### 3.2 Book Catalog Management

**Priority:** High  
**Description:** System must provide comprehensive book catalog management.

#### 3.2.1 Browse and Search Books

**FR-2.1:** The system shall display all available books in the catalog.

**FR-2.2:** The system shall allow users to search books by:
- Title (partial match, case-insensitive)
- Author (partial match, case-insensitive)
- ISBN (exact match)

**FR-2.3:** The system shall provide filters for:
- Category
- Availability status (available/borrowed)
- Publication year range

**FR-2.4:** The system shall display book information:
- Cover image
- Title
- Author
- Category
- Availability (X available / Y total)

**FR-2.5:** The system shall paginate results (20 books per page).

**FR-2.6:** The system shall allow sorting by:
- Relevance (default)
- Title (A-Z)
- Author (A-Z)
- Publication year (newest first)

#### 3.2.2 View Book Details

**FR-2.7:** The system shall display complete book information:
- Cover image (large size)
- Title, Author, ISBN
- Category, Publication year
- Full description
- Total copies and available copies
- Average rating (if applicable)

**FR-2.8:** The system shall show "Borrow" button if:
- User is logged in
- Book is available (available_copies > 0)
- User is eligible to borrow

**FR-2.9:** The system shall show "Add to Shelf" button for logged-in users.

#### 3.2.3 Manage Books (Admin)

**FR-2.10:** Administrators shall be able to add new books with:
- Title (required)
- Author (required)
- ISBN (required, unique)
- Category (required, from predefined list)
- Description (optional)
- Publication year (optional)
- Cover image upload (optional, max 5MB, JPG/PNG)
- Total copies (required, integer > 0)
- Available copies (default = total copies)

**FR-2.11:** The system shall validate all book data before saving.

**FR-2.12:** The system shall generate unique IDs for new books.

**FR-2.13:** Administrators shall be able to edit existing books (all fields except ISBN).

**FR-2.14:** Administrators shall be able to delete books if:
- No active borrowings exist
- Confirmation is provided

**FR-2.15:** The system shall perform soft delete (mark as deleted, not physical removal).

#### 3.2.4 Import from OpenLibrary API

**FR-2.16:** Administrators shall be able to import book data by ISBN.

**FR-2.17:** The system shall call OpenLibrary API with provided ISBN.

**FR-2.18:** The system shall parse and extract:
- Title
- Author(s)
- Description
- Cover image URL
- Publication year

**FR-2.19:** The system shall pre-fill the add book form with imported data.

**FR-2.20:** Administrators shall be able to review/modify before saving.

**FR-2.21:** The system shall handle API errors gracefully (timeout, not found).

---

### 3.3 Borrowing Management

**Priority:** High  
**Description:** System must automate book borrowing and return processes.

#### 3.3.1 Borrow a Book

**FR-3.1:** Users shall be able to borrow available books.

**FR-3.2:** The system shall verify before allowing borrow:
- Book has available copies (available_copies > 0)
- User has no overdue books
- User has not exceeded borrowing limit (max 5 simultaneous)

**FR-3.3:** The system shall create borrowing record with:
- User ID
- Book ID
- Borrow date (current date)
- Due date (borrow date + 14 days)
- Status: 'active'

**FR-3.4:** The system shall decrement available_copies by 1.

**FR-3.5:** The system shall use database transaction to ensure data consistency.

**FR-3.6:** The system shall send confirmation email to user.

**FR-3.7:** The system shall display success message with due date.

**FR-3.8:** The system shall display appropriate error messages for validation failures.

#### 3.3.2 Return a Book

**FR-3.9:** Users shall be able to return borrowed books.

**FR-3.10:** The system shall record return date (current date).

**FR-3.11:** The system shall calculate if return is late (return_date > due_date).

**FR-3.12:** The system shall increment available_copies by 1.

**FR-3.13:** The system shall update borrowing status to 'returned'.

**FR-3.14:** The system shall move borrowing to history.

**FR-3.15:** The system shall display confirmation message.

#### 3.3.3 Extend Borrowing Period

**FR-3.16:** Users shall be able to extend borrowing period once.

**FR-3.17:** The system shall verify:
- Borrowing is active
- Extension not already used
- No reservations exist for the book

**FR-3.18:** The system shall add 7 days to due_date.

**FR-3.19:** The system shall mark borrowing as extended.

**FR-3.20:** The system shall display new due date.

#### 3.3.4 View Borrowing History

**FR-3.21:** Users shall be able to view their complete borrowing history.

**FR-3.22:** The system shall display for each borrowing:
- Book title and cover
- Borrow date
- Due date
- Return date (if returned)
- Status (active/returned/overdue)

**FR-3.23:** The system shall allow filtering by:
- Year
- Category
- Status

**FR-3.24:** The system shall paginate history (20 per page).

#### 3.3.5 Manage Overdue Books (Admin)

**FR-3.25:** The system shall automatically detect overdue books (due_date < current_date AND status = 'active').

**FR-3.26:** Administrators shall see list of all overdue borrowings with:
- User name and email
- Book title
- Due date
- Days overdue

**FR-3.27:** Administrators shall be able to send reminder emails individually or in bulk.

**FR-3.28:** The system shall log all reminder emails sent.

---

### 3.4 Personal Bookshelves

**Priority:** Medium  
**Description:** Users can create and manage personal book collections.

#### 3.4.1 Create Personal Shelf

**FR-4.1:** Users shall be able to create personal bookshelves with:
- Name (required, max 100 characters)
- Description (optional, max 500 characters)
- Visibility (public/private)

**FR-4.2:** The system shall validate shelf name uniqueness per user.

**FR-4.3:** The system shall create shelf with current timestamp.

#### 3.4.2 Manage Shelf Content

**FR-4.4:** Users shall be able to add books to their shelves.

**FR-4.5:** The system shall prevent duplicate books in same shelf.

**FR-4.6:** Users shall be able to add personal notes to shelf books (max 1000 characters).

**FR-4.7:** Users shall be able to remove books from shelves.

**FR-4.8:** Users shall be able to delete entire shelves (with confirmation).

#### 3.4.3 View Shelves

**FR-4.9:** Users shall see list of all their shelves with book counts.

**FR-4.10:** Users shall be able to view shelf contents with:
- Book covers in grid layout
- Personal notes
- Date added

**FR-4.11:** Users shall be able to sort shelf books by:
- Date added (newest first)
- Title (A-Z)
- Author (A-Z)

---

### 3.5 AI-Powered Recommendations

**Priority:** Medium  
**Description:** System provides personalized book recommendations using Google Gemini AI.

#### 3.5.1 Generate Recommendations

**FR-5.1:** The system shall analyze user's reading profile:
- Borrowing history (last 20 books)
- Favorite categories (most borrowed)
- Personal shelf contents

**FR-5.2:** The system shall construct a detailed prompt for Gemini AI including:
- User's reading history
- Preferred categories
- Request for 5-10 book recommendations
- Instruction to explain reasoning

**FR-5.3:** The system shall call Google Gemini AI API with the prompt.

**FR-5.4:** The system shall parse AI response to extract:
- Book titles
- Authors
- Recommendation reasons

**FR-5.5:** The system shall filter recommendations to only include books available in library catalog.

**FR-5.6:** The system shall display recommendations with:
- Book cover, title, author
- AI-generated explanation
- Availability status
- "Borrow" and "Add to Shelf" buttons

**FR-5.7:** The system shall handle API failures gracefully:
- Display error message
- Suggest manual browsing
- Log error for admin review

**FR-5.8:** The system shall cache recommendations for 7 days.

**FR-5.9:** Users shall be able to refresh recommendations manually.

#### 3.5.2 Recommendation Preferences

**FR-5.10:** Users shall be able to specify:
- Preferred categories
- Categories to exclude

**FR-5.11:** The system shall use preferences in future recommendation generation.

---

### 3.6 Forum and Communication

**Priority:** Low  
**Description:** Users can discuss books and communicate.

#### 3.6.1 Forum Posts

**FR-6.1:** Users shall be able to create forum posts with:
- Title (required, max 200 characters)
- Content (required, max 5000 characters)
- Category (required, from predefined list)

**FR-6.2:** The system shall display posts with:
- Title, author, timestamp
- View count
- Comment count

**FR-6.3:** The system shall allow sorting posts by:
- Recent activity
- Most viewed
- Most commented

**FR-6.4:** Users shall be able to edit their own posts within 24 hours.

**FR-6.5:** Administrators shall be able to:
- Pin important posts
- Delete inappropriate posts
- Ban users from forum

#### 3.6.2 Forum Comments

**FR-6.6:** Users shall be able to comment on posts.

**FR-6.7:** The system shall display comments chronologically.

**FR-6.8:** The system shall send notification to post author when commented.

**FR-6.9:** Users shall be able to edit/delete their own comments.

#### 3.6.3 Internal Messaging

**FR-6.10:** Users shall be able to send private messages to other users.

**FR-6.11:** Messages shall include:
- Recipient (required)
- Subject (optional, max 100 characters)
- Message body (required, max 2000 characters)

**FR-6.12:** Users shall have inbox showing:
- Unread count
- Sender, subject, timestamp
- Read/unread status

**FR-6.13:** Users shall be able to mark messages as read/unread.

**FR-6.14:** Users shall be able to delete messages.

---

### 3.7 Administration Dashboard

**Priority:** High  
**Description:** Administrators have comprehensive system overview and management tools.

#### 3.7.1 Dashboard Statistics

**FR-7.1:** The system shall display real-time statistics:
- Total books in catalog
- Total registered users
- Active users (last 30 days)
- Active borrowings count
- Overdue borrowings count
- Books added this month

**FR-7.2:** The system shall display "Top 10 Most Borrowed Books" with:
- Book title and cover
- Borrowing count
- Visual bar chart

**FR-7.3:** The system shall display recent activity feed:
- Recent borrowings
- Recent returns
- New user registrations

#### 3.7.2 User Management

**FR-7.4:** Administrators shall see list of all users with:
- Name, email
- Role (user/admin)
- Status (active/inactive)
- Registration date
- Current borrowings count

**FR-7.5:** Administrators shall be able to:
- Activate/deactivate user accounts
- Change user roles
- View user borrowing history
- Reset user passwords

**FR-7.6:** The system shall prevent deactivating the last admin account.

#### 3.7.3 Reporting

**FR-7.7:** Administrators shall be able to generate reports for:
- Monthly borrowing statistics
- Popular books analysis
- User activity summary
- Overdue book report

**FR-7.8:** Reports shall be exportable to PDF format.

**FR-7.9:** The system shall allow custom date range selection.

---

## 4. External Interface Requirements

### 4.1 User Interfaces

**UI-1:** The system shall provide a responsive web interface that adapts to:
- Desktop (1920x1080 and above)
- Laptop (1366x768)
- Tablet (768x1024)
- Mobile (375x667 minimum)

**UI-2:** The interface shall follow "Library Classic" design theme:
- Warm color palette (parchment, wood, brass)
- Serif typography (Crimson Text for headings, Lora for body)
- Textured backgrounds
- Soft shadows and depth

**UI-3:** All forms shall have:
- Clear labels
- Inline validation
- Error messages in red
- Success messages in green
- Required field indicators (*)

**UI-4:** Navigation shall be consistent across all pages:
- Fixed header with logo and menu
- Breadcrumb navigation where applicable
- Footer with links and copyright

**UI-5:** The system shall provide visual feedback for:
- Loading states (spinners)
- Hover effects on clickable elements
- Active/current page highlighting
- Disabled states

### 4.2 Hardware Interfaces

**HW-1:** No specialized hardware interfaces required.

**HW-2:** The system shall work with standard input devices:
- Keyboard
- Mouse/Trackpad
- Touchscreen (mobile devices)

### 4.3 Software Interfaces

#### 4.3.1 Database Interface

**SW-1:** The system shall interface with MySQL 8.0+ database using:
- Protocol: TCP/IP
- Driver: PHP PDO MySQL
- Connection: Persistent connections for performance
- Character set: UTF-8 (utf8mb4)

**SW-2:** All database operations shall use prepared statements for security.

#### 4.3.2 OpenLibrary API

**SW-3:** The system shall integrate with OpenLibrary Books API:
- Endpoint: `https://openlibrary.org/api/books`
- Method: GET
- Format: JSON
- Authentication: None required
- Rate limit: Respect API guidelines

**SW-4:** The system shall handle API responses:
- Success (200): Parse and extract book data
- Not Found (404): Display "Book not found"
- Timeout: Display "Service unavailable"
- Error (5xx): Log and retry once

#### 4.3.3 Google Gemini AI API

**SW-5:** The system shall integrate with Google Gemini AI:
- Endpoint: `https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent`
- Method: POST
- Format: JSON
- Authentication: API Key in header
- Model: gemini-pro

**SW-6:** The system shall:
- Set timeout to 30 seconds
- Retry once on network failure
- Log all API calls for monitoring
- Handle quota exceeded gracefully

### 4.4 Communication Interfaces

**COM-1:** The system shall use HTTPS (TLS 1.2+) for all communications in production.

**COM-2:** The system shall use HTTP for local development.

**COM-3:** Email notifications shall be sent via:
- Protocol: SMTP
- Port: 587 (TLS) or 465 (SSL)
- Authentication: Username/Password

**COM-4:** The system shall support standard HTTP methods:
- GET: Retrieve resources
- POST: Create resources, form submissions
- PUT/PATCH: Update resources (future)
- DELETE: Remove resources (future)

---

## 5. Non-Functional Requirements

### 5.1 Performance Requirements

**NFR-1.1:** The system shall load the homepage in less than 1 second under normal conditions.

**NFR-1.2:** Search results shall be displayed within 2 seconds for queries on catalogs up to 50,000 books.

**NFR-1.3:** The system shall support at least 100 concurrent users without performance degradation.

**NFR-1.4:** Database queries shall be optimized to execute in less than 500ms on average.

**NFR-1.5:** API calls (OpenLibrary, Gemini) shall have timeout of 10 seconds for OpenLibrary and 30 seconds for Gemini.

**NFR-1.6:** Images (book covers) shall be optimized to load in less than 2 seconds on 3G connection.

**NFR-1.7:** The system shall handle up to 1,000 borrowing transactions per day.

### 5.2 Safety Requirements

**NFR-2.1:** The system shall prevent data corruption through use of database transactions.

**NFR-2.2:** The system shall maintain referential integrity through foreign key constraints.

**NFR-2.3:** The system shall validate all user inputs before processing.

**NFR-2.4:** The system shall prevent simultaneous borrowing of last available copy through database locking.

### 5.3 Security Requirements

**NFR-3.1:** All passwords shall be hashed using bcrypt with cost factor of 12.

**NFR-3.2:** User sessions shall:
- Use secure session IDs (random, 32+ characters)
- Set HttpOnly flag to prevent JavaScript access
- Set Secure flag in production (HTTPS only)
- Expire after 30 minutes of inactivity

**NFR-3.3:** The system shall protect against SQL injection through:
- Prepared statements for all queries
- No dynamic SQL construction from user input

**NFR-3.4:** The system shall protect against XSS (Cross-Site Scripting) through:
- Output escaping (htmlspecialchars)
- Content Security Policy headers

**NFR-3.5:** The system shall protect against CSRF (Cross-Site Request Forgery) through:
- CSRF tokens in all forms
- Token validation on submission

**NFR-3.6:** The system shall implement role-based access control (RBAC):
- Guest: Limited read-only access
- User: Full member privileges
- Admin: All privileges

**NFR-3.7:** The system shall log security events:
- Failed login attempts
- Account privilege changes
- Data deletions

**NFR-3.8:** API keys shall be:
- Stored in configuration files (not in code)
- Excluded from version control (.gitignore)
- Rotated periodically

### 5.4 Software Quality Attributes

#### 5.4.1 Availability

**NFR-4.1:** The system shall have 99.5% uptime (excluding scheduled maintenance).

**NFR-4.2:** Scheduled maintenance shall be announced 48 hours in advance.

**NFR-4.3:** The system shall recover from crashes within 15 minutes.

#### 5.4.2 Maintainability

**NFR-4.4:** The code shall follow MVC architecture pattern.

**NFR-4.5:** The code shall adhere to PSR-12 coding standards for PHP.

**NFR-4.6:** All functions shall have clear, descriptive names.

**NFR-4.7:** Complex logic shall include inline comments.

**NFR-4.8:** The system shall have comprehensive documentation:
- README with installation instructions
- API documentation
- Database schema documentation
- UML diagrams

#### 5.4.3 Portability

**NFR-4.9:** The system shall run on:
- Windows Server
- Linux (Ubuntu, CentOS, Debian)
- macOS (for development)

**NFR-4.10:** The system shall use standard technologies (PHP, MySQL) to ensure portability.

#### 5.4.4 Usability

**NFR-4.11:** New users shall be able to complete basic tasks (browse, search, borrow) within 5 minutes without training.

**NFR-4.12:** All user interface text shall be clear and in plain language.

**NFR-4.13:** Error messages shall:
- Clearly state what went wrong
- Suggest corrective action
- Avoid technical jargon

**NFR-4.14:** The system shall provide help text/tooltips for complex features.

**NFR-4.15:** The system shall maintain visual consistency across all pages.

#### 5.4.5 Scalability

**NFR-4.16:** The database schema shall be normalized to 3NF to support growth.

**NFR-4.17:** The system shall efficiently handle catalogs up to 200,000 books.

**NFR-4.18:** The system shall support horizontal scaling through load balancing (future).

#### 5.4.6 Reliability

**NFR-4.19:** The system shall have automated daily database backups.

**NFR-4.20:** Backups shall be retained for 30 days.

**NFR-4.21:** The system shall gracefully handle external API failures without crashing.

**NFR-4.22:** All errors shall be logged with:
- Timestamp
- Error message
- Stack trace
- User context (if applicable)

### 5.5 Business Rules

**BR-1:** A user can borrow maximum 5 books simultaneously.

**BR-2:** Standard borrowing period is 14 days.

**BR-3:** Borrowing period can be extended once by 7 additional days.

**BR-4:** Users with overdue books cannot borrow new books.

**BR-5:** Books cannot be deleted if they have active borrowings.

**BR-6:** Each user must have a unique email address.

**BR-7:** ISBNs must be unique in the catalog.

**BR-8:** Personal shelf names must be unique per user (different users can use same name).

**BR-9:** Only available books (available_copies > 0) can be borrowed.

**BR-10:** Administrators cannot delete the last admin account.

---

## 6. Other Requirements

### 6.1 Legal Requirements

**LR-1:** The system shall comply with applicable data protection regulations (GDPR, CCPA).

**LR-2:** Users shall be able to request deletion of their personal data.

**LR-3:** The system shall display privacy policy and terms of service.

**LR-4:** Users must accept terms of service before registration.

**LR-5:** All third-party libraries used shall have compatible licenses (MIT, Apache, GPL).

### 6.2 Internationalization

**I18N-1:** The system shall use UTF-8 encoding to support multiple languages.

**I18N-2:** The system shall display dates in configurable format (DD/MM/YYYY or MM/DD/YYYY).

**I18N-3:** The system architecture shall support future multi-language implementation.

### 6.3 Accessibility

**A11Y-1:** The system shall meet WCAG 2.1 Level AA standards.

**A11Y-2:** All images shall have descriptive alt text.

**A11Y-3:** The system shall be navigable by keyboard only.

**A11Y-4:** Color contrast ratio shall be at least 4.5:1 for normal text.

**A11Y-5:** Form inputs shall have associated labels.

---

## 7. Appendix

### 7.1 Glossary

**API (Application Programming Interface):** A set of protocols for building software applications.

**Bcrypt:** A password hashing function designed for secure password storage.

**CRUD:** Create, Read, Update, Delete - basic database operations.

**CSRF (Cross-Site Request Forgery):** A type of security exploit.

**ISBN (International Standard Book Number):** Unique identifier for books.

**MVC (Model-View-Controller):** Software architectural pattern.

**PDO (PHP Data Objects):** Database access layer for PHP.

**RBAC (Role-Based Access Control):** Access control based on user roles.

**REST (Representational State Transfer):** Architectural style for web services.

**SRS (Software Requirements Specification):** Formal documentation of software requirements.

**XSS (Cross-Site Scripting):** Security vulnerability in web applications.

### 7.2 Analysis Models

Refer to the following UML diagrams in `/docs/uml/`:
- Use Case Diagrams (01, 02)
- Sequence Diagrams (03, 04)
- Class Diagram (05)
- Activity Diagram (06)

### 7.3 Issues List

**Open Issues:**
- Fine/penalty calculation algorithm not yet defined
- E-book support deferred to future version
- Multi-library support deferred to future version

---

**Document History:**

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | Feb 2026 | [Your Name] | Initial version |

---

**Approval:**

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Project Manager | | | |
| Development Lead | | | |
| QA Lead | | | |
| Stakeholder | | | |

---

**END OF DOCUMENT**
