#!/bin/bash

set -e

echo "🧪 Setting up functional tests..."

# 1. Ensure .env.test exists and has correct database name
echo "📝 Checking .env.test configuration..."
if ! grep -q "DATABASE_URL.*wtc_test" .env.test 2>/dev/null; then
    echo "⚠️  Updating .env.test with correct database name..."
    sed -i.bak 's/wtc_local_test/wtc_test/g' .env.test 2>/dev/null || true
fi

# 2. Clear test cache
echo "🗑️  Clearing test cache..."
php bin/console cache:clear --env=test
rm -rf var/cache/test

# 3. Drop and recreate test database
echo "🗄️  Setting up test database..."
php bin/console --env=test doctrine:database:drop --force --if-exists
php bin/console --env=test doctrine:database:create

# 4. Run migrations
echo "🔄 Running migrations..."
php bin/console --env=test doctrine:migrations:migrate --no-interaction

# 5. Load fixtures
echo "🌱 Loading test fixtures..."
php bin/console --env=test doctrine:fixtures:load --no-interaction

# 6. Verify database
echo "✅ Verifying database setup..."
php bin/console --env=test dbal:run-sql "SELECT COUNT(*) as user_count FROM users" || {
    echo "❌ Database verification failed"
    exit 1
}

# 7. Test login manually
echo "🔐 Testing login endpoint..."
RESPONSE=$(php bin/console --env=test app:test-login 2>/dev/null || echo "Login test not available")

echo ""
echo "✅ Setup complete!"
echo ""
echo "Run tests with:"
echo "  php vendor/bin/phpunit --testsuite=Functional"