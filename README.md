# Stock Management System

A web-based inventory and stock management system designed to manage products, users, and stock levels across multiple branches with secure access control.

## 🚀 Features
- Role-based access control (Admin, Staff)
- Product and stock management
- Real-time stock tracking across branches
- Sales and operations reporting
- Secure authentication and authorization
- Dockerized environment for easy setup

## 🛠 Tech Stack

### Backend
- Laravel
- Filament
- PHP

### Database
- MySQL

### DevOps / Tools
- Docker
- Docker Compose

## 📁 Project Structure
```
Stock-Management-System/
├── src/
├── docker/
├── docs/
├── docker-compose.yml
├── Dockerfile
├── .env.example
├── start.sh
```

## ⚙️ Environment Setup

Create a `.env` file based on `.env.example` and configure database credentials.

## ▶️ Getting Started

### Run with Docker
```bash
docker-compose up --build
```

### Run locally
```bash
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

## 📌 Status
In development.

## 👤 Author
**Sok Masterly**  
Backend / Full-Stack Developer
