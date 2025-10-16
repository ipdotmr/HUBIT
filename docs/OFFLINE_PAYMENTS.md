# Offline Payments & Multi-Currency Billing

This document explains the offline payment system (bank transfer and cash) with multi-currency support added to HUBIT.

## Overview

The offline payment system allows clients to pay invoices via bank transfer or cash in multiple currencies (MRU, USD, EUR). All payments are subject to admin review before being applied to invoices.

## Key Features

- Multi-currency support (MRU, USD, EUR)
- Offline payment methods (bank transfer, cash)
- Admin review workflow
- Automatic currency conversion
- File upload for payment evidence  
- Reference number generation
- Audit logging

## Database Tables

### currencies
- Stores currency definitions
- Seeded: MRU, USD, EUR

### exchange_rates
- Currency exchange rates
- Manual rates (configurable via settings)

### payment_accounts
- Bank transfer and cash account details
- Encrypted storage
- Per-currency configuration

### payment_transactions
- Payment submissions
- Statuses: pending, under_review, approved, rejected
- Evidence storage
- FX tracking

## Services

### CurrencyService
- convert(amount, from, to)
- getRate(from, to)
- refreshRates()

### PaymentAccountService
- CRUD operations
- getForClient(currency, locale)

### OfflinePaymentService
- requestBankTransfer()
- requestCash()
- review(approve/reject)

## Routes

### Client
- GET /invoices/{id}
- POST /invoices/{id}/pay/bank-transfer
- POST /invoices/{id}/pay/cash

### Admin
- GET /managit/billing/transactions
- POST /managit/billing/transactions/{id}/review
- GET /managit/settings/payments/accounts
- GET /managit/settings/payments/rates
- GET /managit/settings/localization

## UI Components

### Admin
- Settings/PaymentAccounts.vue
- Settings/ExchangeRates.vue
- Settings/Localization.vue
- Billing/Transactions.vue

### Client
- Invoices/Show.vue

## Testing

Location: tests/Feature/Billing/

- OfflinePaymentTest.php
- CurrencyServiceTest.php

Note: Requires SQLite PHP extension

## Future Enhancements

1. Automated exchange rate APIs
2. Partial payments
3. Payment reminders
4. Receipt generation
5. Cryptocurrency support
6. Reporting and analytics
