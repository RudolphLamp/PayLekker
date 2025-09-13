# PayLekker 💰

A modern, secure peer-to-peer payment platform built with PHP, featuring real-time money transfers, budget management, and AI-powered financial assistance.

![PayLekker Dashboard](assets/Sponsors.jpg)

## 🚀 Features

### 💳 Core Payment Features
- **Instant P2P Transfers**: Send money to anyone using their phone number
- **Real-time Balance Updates**: See your balance change instantly
- **Transaction History**: Complete transaction records with search and filtering
- **Secure Authentication**: JWT-based authentication system
- **Add Funds**: Demo fund addition for testing purposes

### 📊 Financial Management
- **Budget Tracking**: Set and monitor monthly budgets
- **Spending Analysis**: Track expenses across different categories
- **Transaction Analytics**: Detailed insights into your spending patterns
- **Real-time Statistics**: Live updates on sent/received amounts

### 🤖 AI Assistant
- **24/7 Support**: Get financial advice anytime
- **Smart Recommendations**: Personalized spending tips
- **Transaction Queries**: Ask questions about your transactions
- **Budget Guidance**: AI-powered budget optimization

### 🎨 Modern UI/UX
- **Clean Design**: Minimalist, professional interface
- **Mobile Responsive**: Works perfectly on all devices
- **Dark Theme Ready**: Clean, modern styling
- **Smooth Animations**: Engaging user experience
- **Accessibility**: Built with accessibility in mind

## 🛠️ Technology Stack

### Backend
- **PHP 8.4+**: Modern PHP with latest features
- **MySQL**: Reliable database for financial data
- **JWT Authentication**: Secure, stateless authentication
- **RESTful API**: Clean, standardized API endpoints

### Frontend
- **Vanilla JavaScript**: No framework dependencies
- **Bootstrap 5**: Modern, responsive components
- **CSS3**: Advanced styling with animations
- **Progressive Enhancement**: Works without JavaScript

### Security
- **Password Hashing**: Secure bcrypt hashing
- **SQL Injection Protection**: Prepared statements
- **XSS Prevention**: Output sanitization
- **CSRF Protection**: Request validation
- **Input Validation**: Comprehensive data validation

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser

## 🔧 Quick Start

### 1. Clone the Repository
```bash
git clone https://github.com/RudolphLamp/PayLekker.git
cd PayLekker
```

### 2. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE paylekker;"

# Run the setup script
cd src
php setup_database.php
```

### 3. Configuration
Update database credentials in `src/database.php`:
```php
$host = 'localhost';
$dbname = 'paylekker';
$username = 'your_username';
$password = 'your_password';
```

### 4. Start Development Server
```bash
cd src
php -S localhost:8000
```

### 5. Access the Application
Open your browser and navigate to `http://localhost:8000`

## 📂 Project Structure  
    ```
PayLekker/
├── src/                          # Main application code
│   ├── assets/                   # Static assets
│   │   ├── css/                  # Stylesheets
│   │   │   ├── dashboard.css     # Main dashboard styling
│   │   │   ├── auth.css          # Authentication styles
│   │   │   ├── landing.css       # Landing page styles
│   │   │   └── main.css          # Global styles
│   │   └── js/                   # JavaScript files
│   │       └── common.js         # Shared utilities
│   ├── auth/                     # Authentication pages
│   │   ├── login.php            # Login page
│   │   └── register.php         # Registration page
│   ├── pages/                    # Application pages
│   │   ├── dashboard.php        # Main dashboard
│   │   ├── transfer-page.php    # Send money page
│   │   ├── history-page.php     # Transaction history
│   │   ├── budget-page.php      # Budget management
│   │   ├── profile-page.php     # User profile
│   │   ├── add-funds-page.php   # Add funds (demo)
│   │   └── chat-page.php        # AI assistant
│   ├── api/                      # API endpoints
│   │   ├── profile.php          # User profile API
│   │   ├── transfer.php         # Money transfer API
│   │   ├── transactions.php     # Transaction history API
│   │   └── chatbot.php          # AI assistant API
│   └── core/                     # Core functionality
│       ├── database.php         # Database connection
│       ├── jwt.php              # JWT authentication
│       ├── response.php         # API response handling
│       └── setup_database.php   # Database setup
├── assets/                       # Public assets
│   ├── Sponsors.jpg             # Hero images
│   └── README.md
├── docs/                         # Documentation
│   ├── SETUP.md                 # Setup instructions
│   ├── TEAM.md                  # Team information
│   ├── USAGE.md                 # Usage guide
│   └── ACKNOWLEDGEMENTS.md      # Credits
├── demo/                         # Demo materials
├── scripts/                      # Utility scripts
└── README.md                     # This file
```

## 🔐 API Documentation

### Authentication
All API endpoints require JWT authentication via the `Authorization: Bearer <token>` header.

### Core Endpoints

#### POST `/src/auth/login.php`
Login with email and password
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
**Response:**
```json
{
  "token": "jwt_token_here",
  "user": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "balance": "1000.00"
  }
}
```

#### POST `/src/auth/register.php`
Register new user account
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "0123456789",
  "password": "password123"
}
```

#### GET `/src/profile.php`
Get user profile information

#### POST `/src/profile.php`
Add funds to user account (demo)
```json
{
  "action": "add_funds",
  "amount": 100.00
}
```

#### POST `/src/transfer.php`
Send money to another user
```json
{
  "recipient_phone": "0987654321",
  "amount": 100.00,
  "description": "Payment for services"
}
```

#### GET `/src/transactions.php`
Get transaction history with pagination
- `limit`: Number of transactions (default: 50, max: 100)
- `offset`: Pagination offset (default: 0)
- `type`: Filter by type (`all`, `sent`, `received`)

## 💡 Key Features

### South African Currency Formatting
All monetary values are displayed in South African Rand (ZAR) format:
- `R 1,234.56` for amounts with commas for thousands
- Consistent formatting across all pages and APIs using `formatCurrency()` function

### Security Features
- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **JWT Tokens**: Secure, stateless authentication with expiration
- **SQL Prepared Statements**: Protection against SQL injection
- **Input Validation**: Server-side validation for all user inputs
- **HTTPS Ready**: Designed to work with SSL/TLS

### Real-time Features
- **Live Balance Updates**: Balance changes instantly after transactions
- **Transaction History**: Real-time transaction updates
- **Responsive UI**: Immediate feedback on all actions
- **Error Handling**: Comprehensive error messages and validation

## 🧪 Demo & Testing

### Demo Users
The system includes demo users for testing:
- **User 1**: john@example.com / password123 (Starting balance: R 1,000.00)
- **User 2**: jane@example.com / password123 (Starting balance: R 500.00)

### Testing Workflow
1. **Registration**: Create new account or use demo users
2. **Login**: Access dashboard with JWT authentication
3. **Add Funds**: Use demo add funds feature to increase balance
4. **Send Money**: Transfer money using recipient's phone number
5. **View History**: Check transaction history with detailed records
6. **Budget Tracking**: Set monthly budgets and track spending
7. **AI Assistant**: Get financial advice and support

## 🚀 Deployment

### Production Checklist
- [ ] Update database credentials in `src/database.php`
- [ ] Enable HTTPS/SSL
- [ ] Set secure JWT secret key
- [ ] Configure proper file permissions (755 for directories, 644 for files)
- [ ] Enable production error handling
- [ ] Set up database backups
- [ ] Configure web server security headers

### Environment Configuration
```php
// Production settings in src/database.php
error_reporting(0);
ini_set('display_errors', 0);
define('JWT_SECRET_KEY', 'your-secure-random-key-here');
define('DB_HOST', 'your-production-database-host');
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Bootstrap team for the amazing CSS framework
- Bootstrap Icons for beautiful iconography
- PHP community for excellent documentation
- MySQL for reliable database technology

## 📧 Contact

**Rudolph Lamp** - [@RudolphLamp](https://github.com/RudolphLamp)

Project Link: [https://github.com/RudolphLamp/PayLekker](https://github.com/RudolphLamp/PayLekker)

---

Made with ❤️ in South Africa 🇿🇦

- **src/**  
    All source code files should be placed in this folder. You may organize this folder as needed (e.g., `backend/`, `frontend/`, `lib/`, `source/` and or `include/` folders and so on).

- **vendor/**  
    All third-party libraries, code and or submodules should be placed in this folder along **with the appropriate licensing and or references**. If you are not able to link the modules from this folder to your codebase properly, you may put the third-party modules inside the `src/` folder with the rest of your code however, it **must be made clear** which modules are **third-party**, along with their **licensing**.
    Since many tech-stacks already use package managers, this `vendor/` folder is for self-included libraries, dependencies and submodules. **Auto-generated** dependency folders like `node_modules/` or `nuget/` should ideally be ignored by `.gitignore`.

- **.dockerignore**  
    Excludes build artifacts and other non-essential files from the Docker image. *You may delete this file if you do not plan on using Docker.*

- **.editorconfig**  
    Standardizes indentation, line endings, and character encoding across editors and platforms. It is **highly recommended** that you use a text editor/IDE that supports **.editorconfig**.

- **.gitattributes**  
    Ensures consistent handling of line endings, text, and binary files across different operating systems.

- **.gitignore**  
    Ignores build artifacts, OS files, IDE configs, and other non-essential files to keep the repository clean.

- **Dockerfile**  
    A "quick start" template **Dockerfile** to serve as a blueprint for containerizing your project in a **Docker image**. *You may delete this file if you do not plan on using Docker.*

- **LICENSE**  
    Default license template for your submission (MIT recommended).
    *You must add the names of your team members to this template.*

- **README.md**  
    Hey wait, that's me!

---

## ✅ Submission Guidelines

1. Create your project's repo off of this template (click the `Use this template` button).  
2. Fill in the `TEAM.md` file with your team members’ information. 
3. Start hacking!
4. Fill in `ACKNOWLEDGEMENTS.md`, `OVERVIEW.md`, `SETUP.md`, `USAGE.md` and `LICENSE`. 
5. Link or include your demo video & PowerPoint in the `demo/` folder.  
6. **Optional:** Include additional documentation and design notes in `docs/`.
7. **Optional:** Include unit tests in `tests/`.
8. Submit the link to your **public GitHub repository**.

---

## 📑 Documentation Checklist

| File                  | Required? | Notes                                                          |
| --------------------- | --------- | -------------------------------------------------------------- |
| `TEAM.md`             | ✅         | Must list all team members, their roles, and institutions      |
| `OVERVIEW.md`         | ✅         | High-level description of your project and its purpose         |
| `SETUP.md`            | ✅         | Instructions to install dependencies and run the project       |
| `USAGE.md`            | ✅         | How to use/test the project after setup                        |
| `ACKNOWLEDGEMENTS.md` | ✅         | Credit all third-party libraries, datasets, and resources used |
| `LICENSE`             | ✅         | Include license type and add your team members’ names          |
| `tests/`              | Optional  | Add test scripts or instructions if relevant                   |
| `Dockerfile`          | Optional  | Only if you choose to containerize your project                |
| Extra docs            | Optional  | Additional guides, design notes, or API references             |

---

## 📌 Tips & Other Remarks

- Keep your code and assets organized within the `src/` and `assets/` directories.  
- Use `.editorconfig` and `.gitattributes` to avoid formatting and line-ending issues.  
- Follow the folder structure strictly — it will make judging smoother and faster.  
- It is highly recommended that you use **Docker** for your submission however, it is **not required**. If you opt to **not** use **Docker**, please ensure that your setup instructions in `SETUP.md` are **straightforward**, **correct**, **comprehensive** and **cross-platform** (if applicable) to ensure that your submission will be graded properly.
- It is also recommended that you work with a **tech-stack** or **build-system** that is **platform-agnostic**. For example: if your project is written in `C++` - which is **platform-dependent**, you may need to ensure that it compiles correctly accross multiple toolchains/compilers for different platforms, thereby creating the added-complexity of having to maintain multiple build-targets - such as having to support both **MSVC for Windows** (using `WIN32` for OS-calls) and **GCC for Linux** (using `POSIX` for OS-calls). However, using a language like `Java` may work much better, since `Java` code is inherently **platform-agnostic** as it runs on a *virtual machine* which abstracts away the lower-level OS-calls.
---

### 💡 Note for First-Time Hackathon Participants
If this is your **first hackathon** or you’re **new to GitHub**, don’t stress — just:  
1. Use this template repo as-is.  
2. Fill in the required documentation files (`TEAM.md`, `OVERVIEW.md`, `SETUP.md`, `USAGE.md`, `ACKNOWLEDGEMENTS.md`, `LICENSE`).  
3. Put your code in the `src/` folder and assets in `assets/`.  

That’s enough for a complete and valid submission 🚀 — the rest (like Docker, tests, extra docs) is **optional polish**.

---

## 🧩 Example Submission
Check out a very basic example submission repository [here](https://github.com/DnA-IntRicate/SAIntervarsityHackathonExampleSubmission2025).

We've also created a **demo video** showcasing the **example submission** and how to get started with this **template repository**, check it out [here](https://youtu.be/e2R9APyatU4).

---

## 🙌 Brought to you by
- [UCT Developer Society](https://www.linkedin.com/company/uct-developers-society)
- [UCT AI Society](https://www.linkedin.com/company/uctaisociety/)
- Stellenbosch AI Society
- [Wits Developer Society](https://www.linkedin.com/company/wits-developer-society/)
- [UJ Developer Society](https://www.linkedin.com/company/uj-developerss-society/)
- [UWC IT Society](https://www.linkedin.com/company/uwc-it-society/)
- [UNISA Developer Society](https://www.linkedin.com/company/unisa-developer-society/)

![Sponsored by](assets/Sponsors.jpg)

### **Good luck and happy hacking!** 🚀
