#!/bin/bash

echo "Setting up test environment..."

# Drop and recreate test database
echo "Setting up test database..."
php bin/console --env=test doctrine:database:drop --force --if-exists
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:migrations:migrate --no-interaction

# Load fixtures
echo "🌱 Loading test fixtures..."
php bin/console --env=test doctrine:fixtures:load --no-interaction

# Clear test cache
echo "Clearing test cache..."
php bin/console --env=test cache:clear

echo "Test environment ready!"
echo ""
echo "Run tests with:"
echo "  php vendor/bin/phpunit                    # All tests"
echo "  php vendor/bin/phpunit --testsuite=Unit   # Unit tests only"
echo "  php vendor/bin/phpunit tests/Unit/Domain/Shared/ValueObject/UuidTest.php  # Specific test"

# Set
# $ chmod +x bin/setup-tests.sh
# $ ./bin/setup-tests.sh