# Web Application Using PHP & MySQL

## 📌 Project Overview

This project is a **simple web application** built with **PHP and MySQL**, designed to demonstrate the following:
- Setting up a local development environment.
- Performing **CRUD (Create, Read, Update, Delete)** operations.
- Implementing **user authentication**.
- Adding **search** and **pagination** features.
- Securing the application with **prepared statements**, **form validation**, and **role-based access control**.

---

## 🎯 Objectives

- ✅ Set up a local PHP development environment.
- ✅ Use **Git** and **GitHub** for version control.
- ✅ Build a functional CRUD application.
- ✅ Implement user authentication.
- ✅ Enhance the app with search, pagination, and improved UI.
- ✅ Secure the app against common web vulnerabilities.
- ✅ Deliver a complete, tested final project.

---

## 🗂️ Project Structure

project-root/

│

├── index.php(blogpost.php)

├── db.php

├── /includes

│ ├── header.php

│ ├── footer.php

│ └── ...

├── /auth

│ ├── register.php

│ ├── login.php

│ ├── logout.php

├── /posts

│ ├── create.php

│ ├── read.php

│ ├── update.php

│ ├── delete.php

├── README.md

└── ...

---

## ⚙️ Setup Instructions

### 1️⃣ Install Local Server Environment

- Download and install **XAMPP**, **WAMP**, or **MAMP**.
- Start **Apache** and **MySQL** services.
- Access `http://localhost` to verify.

### 2️⃣ Install Code Editor

- Recommended: **Visual Studio Code** or **Sublime Text**.
- Add relevant PHP extensions for syntax highlighting and debugging.

### 3️⃣ Set Up Version Control

- Install **Git**.
- Create a **GitHub** account.
- Clone this repository or initialize one in your project folder:
  ```bash
  git init
  git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
  
Make your first commit:
  git add .
  
  git commit -m "Initial commit: Project structure setup"
  
  git push -u origin main


🗃️ Database Setup
Create a MySQL database named blog.

Tables:

users — (id, username, password, role)

posts — (id, title, content, created_at)


🚀 Features


✅ Basic CRUD

Add new posts.

View all posts.

Edit posts.

Delete posts.


🔒 User Authentication

User registration & login.

Password hashing.

Session management.


🔎 Search & Pagination

Search posts by title/content.

Paginated posts list.


🔐 Security

Prepared Statements with PDO/MySQLi.

Server-side & client-side form validation.

Role-based access control (admin, editor, etc.).


📅 Project Timeline

| Task       | Description                                | Timeline |
| ---------- | ------------------------------------------ | -------- |
| **Task 1** | Environment Setup                          | 3 Days   |
| **Task 2** | Basic CRUD Application                     | 10 Days  |
| **Task 3** | Advanced Features (Search, Pagination, UI) | 10 Days  |
| **Task 4** | Security Enhancements                      | 10 Days  |
| **Task 5** | Final Integration & Testing                | 12 Days  |


✅ Deliverables

✔️ Local server environment ready.

✔️ Version-controlled project repository.

✔️ Fully functional CRUD application with authentication.

✔️ Search and pagination features.

✔️ Secure code with prepared statements and validation.

✔️ Documentation for database and security measures.


📚 Documentation

Database schema: see docs/database_schema.sql

Security notes: see docs/security.md


🤝 Contributing

Pull requests and improvements are welcome!

Please fork this repository, create a branch, and submit a pull request.


📄 License

This project is for educational purposes. Feel free to modify and reuse.

