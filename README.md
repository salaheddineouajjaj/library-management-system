# 📚 Advanced Library Management System

A modern, feature-rich library management system with AI-powered book recommendations, personal reading shelves, and an integrated marketplace.

## ✨ Features

### 📖 Core Features
- **Browse Books**: Search and browse thousands of books from OpenLibrary API
- **Personal Library**: Organize books into customizable reading shelves (Want to Read, Currently Reading, Finished)
- **Smart Borrowing System**: Borrow and track books with automated due dates
- **AI-Powered Recommendations**: Get personalized book suggestions using Gemini AI
- **Book Marketplace**: Purchase books with integrated shopping cart

### 🎨 Design
- **Dark Navy Theme**: Professional dark navy background with warm cream content areas
- **Goodreads-Inspired UI**: Beautiful wooden shelf design for personal library
- **Responsive Layout**: Works seamlessly on desktop, tablet, and mobile devices
- **Modern Aesthetics**: Clean cards, smooth animations, and elegant typography

### 🔐 User Management
- **User Authentication**: Secure login/registration system
- **Role-Based Access**: Separate interfaces for users and administrators
- **Profile Management**: Track reading history and borrowing records

### 📊 Admin Dashboard
- **User Management**: View and manage all registered users
- **Book Catalog**: Add, edit, and remove books from the system
- **Borrowing Overview**: Monitor all active and past borrowings
- **Analytics**: View system statistics and usage metrics

## 🛠️ Tech Stack

### Backend
- **PHP 8.1+**: Server-side logic
- **MySQL/MariaDB**: Database management
- **PDO**: Secure database interactions

### Frontend
- **HTML5 & CSS3**: Modern web standards
- **JavaScript**: Interactive features
- **Responsive Design**: Mobile-first approach

### APIs & Services
- **OpenLibrary API**: Book data and cover images
- **Google Gemini AI**: Intelligent book recommendations

## 📋 Prerequisites

- PHP 8.1 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- Composer (optional, for future enhancements)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/YOUR-USERNAME/library-management-system.git
cd library-management-system
```

### 2. Database Setup
```bash
# Import the database schema
mysql -u root -p < database/schema.sql

# Import additional features (optional)
mysql -u root -p < database/updates.sql
mysql -u root -p < database/library_shelves.sql
```

### 3. Configure the Application

Create your configuration files:

**config/config.php**
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'library_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'Library System');
define('TIMEZONE', 'Africa/Casablanca');
define('BASE_URL', 'http://localhost');

define('LOAN_PERIOD_DAYS', 14);
define('MAX_ACTIVE_LOANS', 5);
```

**config/ai_config.php**
```php
<?php
define('AI_API_KEY', 'your-gemini-api-key-here');
define('AI_MODEL', 'gemini-2.0-flash-thinking-exp');
define('AI_ENABLED', true);
```

### 4. Set Up Virtual Host (Optional but Recommended)

**Apache Configuration:**
```apache
<VirtualHost *:80>
    ServerName library.local
    DocumentRoot "C:/xampp/htdocs/v1"
    <Directory "C:/xampp/htdocs/v1">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to hosts file (`C:\Windows\System32\drivers\etc\hosts`):
```
127.0.0.1 library.local
```

### 5. Access the Application

Visit: `http://localhost/v1` or `http://library.local`

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

**Default User Credentials:**
- Username: `user`
- Password: `user123`

⚠️ **Important**: Change default passwords immediately after first login!

## 📁 Project Structure

```
v1/
├── admin/              # Admin panel pages
├── config/             # Configuration files (excluded from git)
├── css/                # Stylesheets
│   ├── style.css       # Main styles
│   ├── navigation.css  # Navigation bar styles
│   ├── library.css     # Personal library styles
│   └── manage-shelf.css # Shelf management styles
├── database/           # SQL schema and migrations
├── includes/           # Reusable PHP components
│   ├── auth_check.php  # Authentication functions
│   ├── nav_user.php    # User navigation
│   └── header.php      # Common header
├── js/                 # JavaScript files
├── user/               # User-facing pages
│   ├── dashboard.php
│   ├── browse_books.php
│   ├── my_library.php
│   └── manage_shelf.php
├── .gitignore          # Git ignore rules
└── README.md           # This file
```

## 🎯 Key Features Explained

### Personal Library Shelves
Users can organize books into three shelves:
- **📋 Want to Read**: Books to read in the future
- **📖 Currently Reading**: Books actively being read
- **✅ Finished**: Completed books with ratings and notes

### AI Book Recommendations
The system uses Google's Gemini AI to provide personalized book recommendations based on:
- User's reading history
- Preferred genres
- Personal interests and mood

### Book Import System
Books can be imported from:
- OpenLibrary API (automatic)
- Manual entry by administrators
- Marketplace purchases

## 🔒 Security Features

- **Password Hashing**: Bcrypt encryption for all passwords
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Output sanitization
- **Session Management**: Secure session handling
- **CSRF Protection**: Token-based form protection (recommended enhancement)

## 🎨 Customization

### Changing Theme Colors
Edit `css/style.css` CSS variables:
```css
:root {
    --color-bg: #0a1628;           /* Dark navy background */
    --color-surface: #f5f1e8;      /* Cream content area */
    --color-primary: #8b7355;      /* Library brown */
    --color-text: #4a3f32;         /* Text color */
}
```

### Adding New Features
1. Create new PHP file in appropriate directory
2. Include authentication check: `require_once '../includes/auth_check.php';`
3. Add navigation link in `includes/nav_user.php`
4. Style with existing CSS or create new stylesheet

## 🐛 Known Issues & Limitations

- Dropdown menus simplified to single-click buttons for better UX
- AI recommendations require valid Google Gemini API key
- Book covers depend on OpenLibrary API availability

## 🚀 Future Enhancements

- [ ] Email notifications for due dates
- [ ] Advanced search filters
- [ ] Book reviews and ratings system
- [ ] Social features (friend recommendations)
- [ ] Reading statistics and analytics
- [ ] Mobile app (PWA)
- [ ] Multi-language support
- [ ] Dark/Light theme toggle

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 👥 Authors

- **Your Name** - Initial work

## 🙏 Acknowledgments

- OpenLibrary API for book data
- Google Gemini AI for recommendations
- The open-source community

## 📞 Support

For support, email your-email@example.com or open an issue on GitHub.

---

Made with ❤️ and 📚
