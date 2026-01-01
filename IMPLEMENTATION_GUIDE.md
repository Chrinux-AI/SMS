# 🚀 VERDANT SMS v4.0 - IMPLEMENTATION GUIDE

## Quick Start (7-Day Launch Plan)

### Prerequisites

- PHP 8.3+
- MySQL 8.0+
- Apache 2.4 with mod_rewrite
- Redis (optional, for caching)
- Node.js (for WebSocket server)

---

## Day 1: Critical Fixes (6 hours)

### Morning (3 hours)

1. **Remove Duplicate Chatbots**

   ```bash
   # Find all chat-related files
   find . -name "*chatbot*.php" -o -name "*chat*.php" | grep -v node_modules

   # Keep only api/ai-copilot.php
   # Delete duplicates
   ```

2. **Fix Navigation Sidebar**

   ```bash
   # Scan for broken links
   php scripts/check_links.php

   # Fix all broken hrefs in includes/cyber-nav.php
   ```

### Afternoon (3 hours)

3. **Create Error Pages**

   - [x] 404.php (already created)
   - [ ] 403.php
   - [ ] 500.php
   - [ ] maintenance.php

4. **UI Consistency Check**
   ```bash
   # Find files missing cyberpunk-ui.css
   grep -rL "cyberpunk-ui.css" --include="*.php" | grep -v vendor
   ```

---

## Day 2: Security & Environment (4 hours)

### Morning (2 hours)

1. **Set up .env file**

   ```bash
   cp .env.example .env
   # Edit .env with your values
   ```

2. **Disable TEST_MODE**
   ```php
   // includes/config.php
   define('TEST_MODE', getenv('DEV_MODE') === 'true' ? true : false);
   ```

### Afternoon (2 hours)

3. **Add CSRF Protection**

   ```bash
   # Implement in includes/csrf.php
   # Add to all forms
   ```

4. **Create Favicon**
   ```bash
   # Design 16x16, 32x32, 192x192, 512x512 versions
   # Place in assets/images/icons/
   ```

---

## Day 3-4: Messaging Foundation (16 hours)

### Database Setup

```bash
mysql -u root -p attendance_system < database/messaging_schema.sql
```

### WebSocket Server

```bash
composer require cboden/ratchet
php server/websocket_server.php &
```

### Chat UI

- Create `/chat/index.php`
- Implement conversation list
- Build message display
- Add message input

---

## Day 5-6: Messaging Features (16 hours)

### Implement

- Real-time message delivery
- Read receipts
- Typing indicators
- File sharing
- Voice notes (optional)

---

## Day 7: Testing & Launch (8 hours)

### Morning

1. **Run Tests**

   ```bash
   ./vendor/bin/phpunit
   ```

2. **Performance Audit**
   ```bash
   # Use Chrome DevTools Lighthouse
   # Target: 90+ performance score
   ```

### Afternoon

3. **Security Scan**

   ```bash
   # Check for vulnerabilities
   # Verify all forms have CSRF
   # Test rate limiting
   ```

4. **Deploy to Production**
   ```bash
   git add .
   git commit -m "v4.0.0 - Production ready with advanced features"
   git push origin main
   ```

---

## Post-Launch Checklist

- [ ] Monitor error logs
- [ ] Track user feedback
- [ ] Optimize slow queries
- [ ] Set up automated backups
- [ ] Configure monitoring (Sentry, etc.)

---

## Common Commands

### Development

```bash
# Start local server
php -S localhost:8000

# Watch for file changes (if using build tools)
npm run watch

# Clear cache
php scripts/clear_cache.php
```

### Maintenance

```bash
# Backup database
php scripts/backup_db.php

# Run migrations
php scripts/migrate.php

# Check system health
php scripts/health_check.php
```

---

## Troubleshooting

### Issue: White pages

**Solution**: Ensure all pages have `cyber-bg` class on body

### Issue: Navigation not working

**Solution**: Check `includes/cyber-nav.php` for broken links

### Issue: Messages not sending

**Solution**: Verify WebSocket server is running on port 8080

---

## Support

- GitHub: https://github.com/Chrinux-AI/SMS
- Email: christolabiyi35@gmail.com
- WhatsApp: +2348167714860

---

**Last Updated**: 25 December 2025
**Version**: 4.0.0
