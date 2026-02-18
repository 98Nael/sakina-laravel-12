# Inertia.js Login Page Setup - Installation Guide

## What's Been Set Up

A complete Inertia.js authentication system with Vue 3 has been implemented in your Laravel application. This includes:

### ✅ Components Created

1. **Login Page** (`resources/js/Pages/Login.vue`) - Beautiful login UI with:
   - Email and password fields
   - Remember me checkbox
   - Error message display
   - Loading state
   - Responsive design with Tailwind CSS
   - Gradient background

2. **Dashboard Page** (`resources/js/Pages/Dashboard.vue`) - Role-based dashboard with:
   - User information display
   - Role-specific navigation
   - Quick stats cards
   - Logout functionality

### 📦 Installed Dependencies

- `@inertiajs/vue3` - Vue 3 adapter for Inertia
- `vue` - Vue 3 framework
- `@vitejs/plugin-vue` - Vite plugin for Vue

### 🔧 Files Modified/Created

- `vite.config.js` - Updated with Vue plugin
- `resources/js/app.js` - Updated to initialize Inertia with Vue
- `resources/views/app.blade.php` - Main Blade template for Inertia
- `bootstrap/app.php` - Added Inertia middleware
- `routes/web.php` - Added login and dashboard routes
- `app/Http/Controllers/LoginController.php` - Login logic controller
- `app/Http/Controllers/DashboardController.php` - Dashboard controller

## 🚀 Getting Started

### 1. Start Development Server

Open two terminal windows:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

Or use the dev command to run both:
```bash
composer dev
```

### 2. Test Login Credentials

Use these test accounts (all with password: `password`):

| Email | Role | Purpose |
|-------|------|---------|
| admin@example.com | admin | Full system access |
| doctor@example.com | doctor | Medical portal access |
| patient@example.com | patient | Patient portal access |
| test@example.com | user | Regular user |

### 3. Access the Application

1. Navigate to `http://localhost:8000/login`
2. Enter credentials from above
3. Submit to login
4. You'll be redirected to `/dashboard`
5. Dashboard displays role-specific content

## 📝 Project Structure

```
resources/
  ├── js/
  │   ├── app.js                 # Inertia app initialization
  │   ├── bootstrap.js           # Global config
  │   └── Pages/
  │       ├── Login.vue          # Login page component
  │       └── Dashboard.vue      # Dashboard page component
  └── views/
      └── app.blade.php          # Main HTML template

app/Http/Controllers/
  ├── LoginController.php        # Login/logout logic
  └── DashboardController.php    # Dashboard logic

routes/
  └── web.php                    # Route definitions
```

## 🎨 Design Features

- **Modern UI**: Gradient backgrounds, smooth transitions
- **Responsive**: Works on mobile, tablet, and desktop
- **Tailwind CSS**: Utility-first CSS framework
- **Form Validation**: Client and server-side validation
- **Error Display**: Clear error messages on login failure
- **Loading States**: Visual feedback during authentication

## 🔐 Authentication Flow

1. **Guest Access**: Unauthenticated users can access `/login`
2. **Login Attempt**: POST to `/login` with credentials
3. **Validation**: Server validates email and password
4. **Authentication**: Checks against database
5. **Session Creation**: On success, session is created
6. **Role-Based Redirect**: Redirects based on user role
7. **Dashboard Access**: Authenticated users can access `/dashboard`
8. **Logout**: POST to `/logout` destroys session

## 🛡️ Security Features

- CSRF protection (built into Inertia)
- Password hashing (bcrypt)
- Session management
- Role-based access control
- Authentication middleware (`auth`)
- Guest middleware (`guest`)

## 📱 Using the Login Page

### HTML Form Submission
The login form is a Vue component that:
- Validates input in real-time
- Shows/hides errors dynamically
- Displays loading state during submission
- Handles "Remember me" functionality

### Error Handling
Validation errors display at the top of the form:
- Invalid email format
- Missing required fields
- Incorrect credentials

## 🔧 Using Inertia in Your App

### Redirecting to Inertia Pages

```php
use Inertia\Inertia;

return Inertia::render('PageName', [
    'prop' => 'value',
]);
```

### Using Links in Vue Components

```vue
<Link href="/dashboard" class="btn">Go to Dashboard</Link>
```

### Accessing Props in Components

```javascript
defineProps({
    user: Object,
    appName: String,
});
```

### Form Submission

```javascript
import { router } from '@inertiajs/vue3';

router.post('/login', {
    email: form.email,
    password: form.password,
});
```

## 🎯 Next Steps

1. **Create More Pages**: Add admin, doctor, and patient dashboard pages
2. **Implement Authorization**: Use middleware to protect role-specific routes
3. **Add Navigation**: Create a main navigation component
4. **Style Customization**: Modify Tailwind classes to match your brand
5. **API Integration**: Connect to backend APIs for data manipulation

## 📚 Useful Resources

- [Inertia.js Documentation](https://inertiajs.com)
- [Vue 3 Documentation](https://vuejs.org)
- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Laravel Authentication Docs](https://laravel.com/docs/authentication)

## 🐛 Troubleshooting

### "Inertia page not found"
- Ensure `npm run dev` is running
- Check file structure in `resources/js/Pages/`
- Verify file names match component names (capitalized)

### "CSS not loading"
- Make sure Vite dev server is running
- Clear browser cache (Ctrl+Shift+Delete)
- Check `@vite` directive in `app.blade.php`

### "Login not working"
- Verify database has been migrated and seeded
- Check `.env` database configuration
- Ensure user exists in database
- Check browser console for errors

### "Styles not applied"
- Verify Tailwind is properly configured
- Check that VITE_APP_NAME is set in .env
- Rebuild with `npm run build`

## 🎓 Learning Path

1. Explore the login flow by examining `LoginController.php`
2. Study the Vue component structure in `Login.vue`
3. Understand route protection with middleware
4. Create new pages following the same pattern
5. Build out role-specific dashboards

---

**Your application is now ready with a complete Inertia.js authentication system!**
