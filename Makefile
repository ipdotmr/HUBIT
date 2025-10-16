.PHONY: ci lint test dusk build help

help:
	@echo "HUBIT Development Makefile"
	@echo ""
	@echo "Available targets:"
	@echo "  ci        - Run full CI pipeline locally (lint + test + dusk)"
	@echo "  lint      - Run Laravel Pint formatter check"
	@echo "  test      - Run PHPUnit tests (excludes quarantine)"
	@echo "  dusk      - Run Dusk browser tests"
	@echo "  build     - Build frontend assets"
	@echo "  help      - Show this help message"

ci: lint test dusk

lint:
	cd app && ./vendor/bin/pint --test

test:
	cd app && vendor/bin/phpunit

dusk:
	cd app && php artisan dusk

build:
	cd app && npm ci --legacy-peer-deps && npm run build
