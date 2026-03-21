# CletaEats Web App – Prototype

## Overview
This repository contains the **first functional prototype** of the CletaEats Web Application.  
The goal of this prototype is to implement the initial core modules required for user access, navigation, and basic entity management, following a clean and maintainable architecture.

This web app is part of a larger system that includes a mobile application. For this stage, **only the web application is implemented**.

---

## Scope of the Prototype

The prototype includes the following modules:

### 1. Authentication (Login & Registration Flow)
- User login with username and password
- Secure authentication with backend validation
- Controlled user registration (admin-managed)
- Session handling and logout functionality

### 2. Navigation (Hamburger Menu / Nav Drawer)
- Responsive navigation drawer
- Menu options for navigating the system
- Logout option
- Logo placeholder for branding

### 3. Customer Management (CRUD)
- List view (table/card responsive layout)
- Create, Read, Update, Delete operations
- Search functionality (individual and full search)
- Floating Action Button (FAB) for adding customers
- Edit and delete actions with confirmation
- Form validation (client and server side)

---

## Technologies

- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript
- **Database:** Remote MySQL
- **Architecture:** Layered structure with DRY and SOLID principles

---

## Database Connection

The system connects to a remote MySQL database.  
Credentials are handled securely on the backend (not exposed to the client).

> Configuration must be placed in a local environment file or ignored config file.

---

## Design Principles

- Clean and maintainable code
- DRY (Don't Repeat Yourself)
- SOLID principles
- Secure coding practices
- Responsive and professional UI/UX

---

## Project Structure (Planned)

The project follows a modular structure separating concerns such as:
- Controllers
- Services
- Repositories
- Views
- Configurations

> The exact structure will be defined during implementation.

---

## How to Run (Planned)

1. Install a local PHP server (XAMPP, Laragon, or similar)
2. Clone the repository:
   ```bash
   git clone https://github.com/your-username/cletaeats-webapp.git