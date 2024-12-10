# GIFT REQUEST SOLUTION v1.0

A comprehensive gift management system with multi-level user roles and inventory tracking.

## Features

### 1. Multi-level User Roles
- **Admin**: Full system management
- **Manager**: Enterprise-level management
- **Distributor**: Gift request creation within limits

### 2. Product Management
- Master Products catalog
- Enterprise-specific inventory
- Stock validation for requests

### 3. Gift Request Management
- Create requests with recipient details
- Status tracking (pending/processing/shipping/completed)
- Automated notifications via webhook

### 4. Distributor Management
- Creation and management by enterprise managers
- Request limits and tracking
- Usage monitoring

### 5. Role-based Dashboard & Reports
- Admin: System-wide view
- Manager: Enterprise scope
- Distributor: Personal scope

## Technical Stack
- PHP
- MySQL
- Bootstrap
- MAKE.com webhooks integration

## Database Schema
- users
- enterprises
- master_products
- inventory
- requests

## Installation
1. Clone the repository
2. Import database schema
3. Configure database connection
4. Set up webhook endpoints
5. Configure permissions for uploads and logs directories