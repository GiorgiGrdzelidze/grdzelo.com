# grdzelo.com

> Premium personal brand website + CMS with advanced multi-currency finance management

A sophisticated Laravel 13 + Filament v5 application combining a personal brand website with a powerful private finance system, featuring multi-currency support, subscription management, and comprehensive content management.

## ✨ Features

### 🌐 Public Website
- **Modern Tech Stack**: Vue 3 + TypeScript + Tailwind CSS v4 + shadcn-vue
- **SEO Optimized**: Full SSR support, sitemap generation, schema.org markup
- **Premium Design**: Clean, responsive UI with dark/light theme support
- **Content Management**: Blog, projects, services, skills, experience, education, certifications
- **Contact System**: Form submissions with admin notifications

### 💰 Finance System
- **Multi-Currency Support**: Track income/expenses in GEL, USD, EUR with automatic conversion
- **Exchange Rate Management**: Historical rate tracking with manual entry
- **Subscription Lifecycle**: Full subscription management with pause/resume/cancel actions
- **Automated Reminders**: Email notifications before subscription renewals
- **Salary Records**: Tax calculations, annual projections, payment frequency tracking
- **Advanced Reporting**: Currency-aware widgets and comprehensive analytics

### 🎓 Education & Certifications
- **Academic Background**: Education timeline with achievements and logos
- **Professional Certifications**: Credential tracking with expiry management
- **Skill Integration**: Link certifications to relevant skills
- **Public Display**: Beautiful frontend pages matching site design

### 🛠️ Admin Panel
- **Filament v5**: Modern, responsive admin interface
- **Resource Management**: Full CRUD for all content types
- **Finance Dashboard**: Real-time statistics and insights
- **Settings Management**: SEO, finance, and general site configuration
- **Role-Based Access**: Secure authentication with 2FA support

## 🏗️ Architecture

### Backend
- **Framework**: Laravel 13.2.0
- **PHP**: 8.5.3 with strict typing
- **Database**: MySQL with comprehensive migrations
- **Packages**:
  - Filament v5.4.3 (admin panel)
  - Inertia.js v3 (SPA)
  - Spatie (media library, settings, activitylog, sitemap, schema-org)
  - Fortify (authentication)

### Frontend
- **Framework**: Vue 3.5 + TypeScript
- **Styling**: Tailwind CSS v4 + shadcn-vue components
- **Icons**: Lucide Vue Next
- **Build Tool**: Vite 8
- **SSR**: Enabled (port 13714)

### Development Environment
- **Containerization**: Docker Sail
- **Environment**: Local development with hot reload
- **Code Quality**: ESLint, Prettier, PHP 8.5 strict typing

## 📊 Finance System Details

### Multi-Currency Architecture
- **Base Currency**: Configurable (default: GEL)
- **Supported Currencies**: GEL, USD, EUR (extensible)
- **Exchange Rates**: Historical tracking with manual entry
- **Automatic Conversion**: Base amount calculations for unified reporting

### Income Management
- **Income Sources**: Categorized sources with recurring options
- **Income Entries**: Transaction tracking with currency snapshots
- **Salary Records**: Detailed employment records with tax calculations
- **Annual Projections**: Automatic yearly income calculations

### Expense Tracking
- **Expense Categories**: Organized spending categories
- **Multi-Currency Expenses**: Track spending in any supported currency
- **Recurring Expenses**: Automated expense tracking
- **Merchant Information**: Detailed expense metadata

### Subscription Management
- **Lifecycle Control**: Active, paused, cancelled, expired states
- **Billing Intervals**: Weekly, monthly, quarterly, yearly, custom
- **Event History**: Complete audit trail of subscription changes
- **Automated Reminders**: Configurable email notifications
- **Cost Analytics**: Monthly/yearly subscription cost tracking

## 🎓 Education & Certifications

### Education Module
- **Institution Details**: School/university information with logos
- **Degree Tracking**: Academic achievements and field of study
- **Timeline Display**: Duration with current/past status
- **Achievement Lists**: Academic accomplishments and honors

### Certification System
- **Credential Management**: Issuing organization, dates, IDs
- **Expiry Tracking**: Automatic expiry detection and notifications
- **Skill Integration**: Link certifications to relevant skills
- **Verification Links**: Direct links to credential verification
- **Badge Display**: Visual certification badges


## 📁 Project Structure

```
├── app/
│   ├── Enums/                 # PHP 8.5 backed enums
│   ├── Models/                # Eloquent models
│   ├── Filament/              # Admin panel resources
│   ├── Http/Controllers/      # Web controllers
│   └── Settings/              # Spatie settings
├── database/
│   └── migrations/            # Database migrations
├── resources/js/
│   ├── pages/Public/          # Vue frontend pages
│   ├── components/ui/          # shadcn-vue components
│   └── layouts/               # Layout components
├── routes/
│   ├── web.php                # Public routes
│   └── console.php            # Artisan commands
└── storage/app/public/        # User uploads
```

## 🔧 Configuration

### Finance Settings
Navigate to **Admin → Settings → Finance Settings** to configure:
- Base currency
- Supported currencies
- Default tax percentage
- Fiscal year start
- Reminder settings

### SEO Settings
Configure site-wide SEO defaults, Open Graph, Twitter Cards, and schema markup in **Admin → Settings → SEO Settings**.

### Subscription Reminders
The system automatically:
- Generates reminders daily at 06:00
- Sends notifications at 08:00
- Uses configured reminder offsets


## 🌟 Key Features Deep Dive

### Multi-Currency Finance
- **Real-time Conversion**: Exchange rate snapshots at transaction time
- **Base Reporting**: All financial reports normalized to base currency
- **Historical Accuracy**: Preserves original amounts and rates
- **Flexible Currencies**: Easy to extend with new currency support

### Subscription Lifecycle
- **State Management**: Full subscription lifecycle tracking
- **Event Logging**: Complete audit trail of all changes
- **Automated Actions**: Pause, resume, cancel with proper state transitions
- **Cost Analytics**: Monthly/yearly cost projections

### Content Management
- **Rich Editor**: Advanced content editing with media support
- **SEO Integration**: Automatic metadata generation
- **Media Library**: Organized file management
- **Version Control**: Activity logging for all changes

## 🔒 Security

- **Authentication**: Laravel Fortify with 2FA
- **Authorization**: Role-based access control
- **CSRF Protection**: Built-in Laravel protection
- **XSS Prevention**: Proper input sanitization
- **SQL Injection**: Eloquent ORM protection

## 📈 Performance

- **SSR**: Server-side rendering for fast initial load
- **Caching**: Multiple caching layers
- **Optimized Queries**: Eloquent relationship optimization
- **Asset Optimization**: Vite production builds
- **Database Indexing**: Proper indexing for performance


## 📄 License

This project is proprietary software. All rights reserved.

## 📞 Support

For support and inquiries, please use the contact form on the website.

---

**Built with ❤️ using Laravel, Vue 3, and Tailwind CSS**
