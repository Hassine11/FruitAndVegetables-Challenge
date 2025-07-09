# 🍓🥬🚐 Fruits & Vegetables Challenge

¡Hola!
Welcome to the **Fruits & Vegetables Challenge** — a Symfony 7.3 project powered by **PHP 8.3.16** and **MariaDB**.
This project is containerized using Docker 🐳 for a smooth, consistent development experience. It includes:

✅ API documentation
✅ Database migrations
✅ Initial data import
✅ Testing with Pest
✅ Static analysis & code quality tools
🔥 Code coverage: **90%+**
🔥 Psalm coverage: **93%+**

---

## 🚀 Getting Started

### 📦 1. Clone the Repository

```bash
git clone https://github.com/Hassine11/FruitAndVegetables-Challenge.git
cd /FruitAndVegetables-Challenge
```

---

### 🐳 2. Start Docker Containers

```bash
docker-compose up --build -d
```

This will start:
- `php` – Symfony app container (PHP-FPM)
- `db` – MariaDB container

---

### ⚙️ 3. Configure Environment Variables

#### 🔧 Edit `.env`

```env
DATABASE_URL="mysql://username:password@db:3306/roadsurferdb?serverVersion=8.0.32&charset=utf8mb4"
```

---

### 🧱 4. Create Database & Run Migrations

```bash
docker-compose exec php php bin/console doctrine:database:create
docker-compose exec php php bin/console doctrine:migrations:migrate
```

---

## 🧪 Test Environment Setup

### ⚙️ 5. Configure `.env.test`

```env
APP_ENV=test
APP_DEBUG=1
DATABASE_URL="mysql://username:password@db:3306/roadsurferdb_test"
```

---

### 🧱 6. Create & Migrate Test Database

```bash
docker-compose exec php php bin/console doctrine:database:create --env=test
docker-compose exec php php bin/console doctrine:migrations:migrate --env=test
```

---

## 🧰 Development Tools

### 🐚 Access PHP Container

```bash
docker exec -it roadsurfer-php bash
```

---

### 📥 Initial Import of Inventory Data

Make sure your `request.json` is ready, then run:

```bash
docker-compose exec php php bin/console app:import:inventory
```

---

### ✅  Run Full Commit Checks (Inside PHP Container)

This command runs:

- 🧹 PHP-CS-Fixer (code formatting)
- 🔍 Psalm (static analysis)
- 🧪 Pest (tests)

```bash
composer commit
```

> 🔥 Code coverage: **90%+**
> 🔥 Psalm coverage: **93%+**

---

## 📘 API Documentation

Access the API spec:

🌐 [http://localhost:8080/api_specification/inventory/inventory.yaml](http://localhost:8080/api_specification/inventory/inventory.yaml)

---

## ✅ You're Ready!

Project is now up and ~~running~~ Vanning 🚐 .
Waiting For Feedbacks! 🚐🚐🚐
