#!/bin/bash

echo "========================================="
echo "WeFix.lk API Setup Script"
echo "========================================="
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.1 or higher."
    exit 1
fi

echo "✓ PHP found: $(php -v | head -n 1)"
echo ""

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed."
    echo "Please install Composer from: https://getcomposer.org"
    exit 1
fi

echo "✓ Composer found: $(composer --version | head -n 1)"
echo ""

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

if [ $? -ne 0 ]; then
    echo "❌ Composer install failed"
    exit 1
fi

echo "✓ Dependencies installed"
echo ""

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✓ .env file created"
else
    echo "✓ .env file already exists"
fi

echo ""

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

if [ $? -ne 0 ]; then
    echo "❌ Failed to generate application key"
    exit 1
fi

echo "✓ Application key generated"
echo ""

# Ask for database configuration
echo "📊 Database Configuration"
echo "-------------------------"
read -p "Database name [wefix_db]: " DB_NAME
DB_NAME=${DB_NAME:-wefix_db}

read -p "Database user [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -s -p "Database password: " DB_PASSWORD
echo ""

read -p "Database host [127.0.0.1]: " DB_HOST
DB_HOST=${DB_HOST:-127.0.0.1}

read -p "Database port [3306]: " DB_PORT
DB_PORT=${DB_PORT:-3306}

# Update .env with database config
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env

echo ""
echo "✓ Database configuration updated"
echo ""

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate

if [ $? -ne 0 ]; then
    echo "❌ Migration failed. Please check your database connection."
    echo ""
    echo "Manual setup steps:"
    echo "1. Create database: CREATE DATABASE $DB_NAME;"
    echo "2. Run: php artisan migrate"
    exit 1
fi

echo "✓ Migrations completed successfully"
echo ""

# Ask if user wants to seed database
read -p "Do you want to seed the database with test data? (y/n): " SEED_DB

if [ "$SEED_DB" = "y" ] || [ "$SEED_DB" = "Y" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --class=WefixSeeder
    
    if [ $? -eq 0 ]; then
        echo "✓ Database seeded successfully"
        echo ""
        echo "📝 Test Credentials:"
        echo "   Admin: wefixtvrepair@gmail.com / admin123"
        echo "   Customer: customer@example.com / customer123"
    else
        echo "⚠️  Database seeding failed (optional)"
    fi
fi

echo ""
echo "========================================="
echo "✅ Setup Complete!"
echo "========================================="
echo ""
echo "🚀 To start the server, run:"
echo "   php artisan serve"
echo ""
echo "📚 API Documentation:"
echo "   - Read: WEFIX_API_README.md"
echo "   - Postman: WeFix_API_Postman_Collection.json"
echo ""
echo "🔗 API Base URL:"
echo "   http://localhost:8000/api"
echo ""
echo "========================================="
