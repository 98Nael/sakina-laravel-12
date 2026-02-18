# Inertia.js Implementation Examples

## Table of Contents
1. [Creating Login/Register Forms](#creating-loginregister-forms)
2. [Route Protection](#route-protection)
3. [Using User Data](#using-user-data)
4. [Navigation Links](#navigation-links)
5. [Form Handling](#form-handling)
6. [Error Display](#error-display)

## Creating Login/Register Forms

### Basic Login Form (Current Implementation)

```vue
<template>
  <form @submit.prevent="handleLogin">
    <input v-model="form.email" type="email" placeholder="Email" />
    <input v-model="form.password" type="password" placeholder="Password" />
    <button :disabled="form.processing">
      {{ form.processing ? 'Logging in...' : 'Login' }}
    </button>
  </form>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const form = reactive({
  email: '',
  password: '',
  processing: false,
});

const handleLogin = async () => {
  form.processing = true;
  
  router.post('/login', {
    email: form.email,
    password: form.password,
  }, {
    onFinish: () => {
      form.processing = false;
    },
  });
};
</script>
```

### Registration Form Example

```vue
<template>
  <form @submit.prevent="handleRegister">
    <input v-model="form.name" type="text" placeholder="Full Name" />
    <input v-model="form.email" type="email" placeholder="Email" />
    <input v-model="form.password" type="password" placeholder="Password" />
    <input v-model="form.password_confirmation" type="password" placeholder="Confirm Password" />
    
    <!-- Error Display -->
    <div v-if="errors" class="error-message">
      <p v-for="error in Object.values(errors)" :key="error">{{ error }}</p>
    </div>
    
    <button :disabled="form.processing">Register</button>
  </form>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
  errors: Object,
});

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  processing: false,
});

const handleRegister = () => {
  form.processing = true;
  
  router.post('/register', form, {
    onFinish: () => {
      form.processing = false;
    },
  });
};
</script>
```

### Corresponding RegisterController

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function create()
    {
        return Inertia::render('Register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        auth()->login($user);

        return redirect('/dashboard');
    }
}
```

## Route Protection

### Protecting Routes with Middleware

```php
// routes/web.php

// Only authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

// Only guests (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Role-based routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard']);
});

Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/patient/dashboard', [PatientController::class, 'dashboard']);
});
```

### Custom Role Middleware Example

```php
// app/Http/Middleware/CheckRole.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        if (auth()->user()->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}

// Register in bootstrap/app.php
$middleware->alias([
    'role' => \App\Http\Middleware\CheckRole::class,
]);

// Use in routes
Route::middleware('role:admin,doctor')->group(function () {
    Route::get('/medical', MedicalController::class);
});
```

## Using User Data

### Accessing Authenticated User in Controller

```php
// In any controller with auth middleware
public function show(Request $request)
{
    $user = $request->user(); // Current authenticated user
    
    return Inertia::render('Dashboard', [
        'user' => $user,
        'role' => $user->role,
        'isAdmin' => $user->isAdmin(),
    ]);
}
```

### Using User Data in Vue Components

```vue
<template>
  <div>
    <h1>Welcome, {{ user.name }}!</h1>
    <p>Email: {{ user.email }}</p>
    <p>Role: {{ user.role }}</p>
    
    <!-- Conditional rendering based on role -->
    <div v-if="user.role === 'admin'">
      <button @click="openAdminPanel">Admin Panel</button>
    </div>
    
    <div v-else-if="user.role === 'doctor'">
      <button @click="viewPatients">View Patients</button>
    </div>
    
    <div v-else-if="user.role === 'patient'">
      <button @click="viewAppointments">My Appointments</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  user: Object,
});
</script>
```

### Using usePage Composable

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.user;
const appName = page.props.appName;

// Reactive to updates
watch(() => page.props.user, (newUser) => {
  console.log('User updated:', newUser);
});
</script>
```

## Navigation Links

### Using Inertia Link Component

```vue
<template>
  <!-- Simple link -->
  <Link href="/dashboard" class="btn">Dashboard</Link>

  <!-- With parameters -->
  <Link :href="`/users/${user.id}/edit`" method="get">Edit User</Link>

  <!-- Form submission -->
  <Link href="/logout" method="post">Logout</Link>

  <!-- With confirmation -->
  <Link href="/delete" method="delete" as="button" @click="confirm('Delete?')">
    Delete Account
  </Link>

  <!-- Preserve scroll -->
  <Link href="/posts" preserve-scroll>Posts</Link>

  <!-- Preserve state (e.g., form inputs) -->
  <Link href="/posts" preserve-state>Back to Posts</Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
```

## Form Handling

### Simple Form with router.post()

```vue
<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const form = reactive({
  name: '',
  email: '',
});

const submit = () => {
  router.post('/users', form);
};
</script>
```

### Form with useForm() Composable

```vue
<template>
  <form @submit.prevent="form.post('/users')">
    <input v-model="form.name" type="text" />
    <input v-model="form.email" type="email" />
    
    <!-- Show errors -->
    <div v-if="form.errors.name" class="error">{{ form.errors.name }}</div>
    <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
    
    <button :disabled="form.processing">
      {{ form.processing ? 'Saving...' : 'Save' }}
    </button>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
});
</script>
```

### Handling File Uploads

```vue
<template>
  <form @submit.prevent="submit">
    <input type="file" @change="form.avatar = $event.target.files[0]" />
    <button :disabled="form.processing">Upload</button>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  avatar: null,
});

const submit = () => {
  form.post('/avatar', {
    onSuccess: () => alert('Avatar updated!'),
  });
};
</script>
```

## Error Display

### Display Server-Side Validation Errors

```vue
<template>
  <div class="form">
    <!-- General errors -->
    <div v-if="errors.general" class="alert alert-danger">
      {{ errors.general }}
    </div>
    
    <!-- Field-specific errors -->
    <div class="form-group">
      <label>Email</label>
      <input v-model="form.email" type="email" />
      <span v-if="errors.email" class="error">{{ errors.email }}</span>
    </div>
    
    <div class="form-group">
      <label>Password</label>
      <input v-model="form.password" type="password" />
      <span v-if="errors.password" class="error">{{ errors.password }}</span>
    </div>
    
    <button @click="handleLogin">Login</button>
  </div>
</template>

<script setup>
defineProps({
  errors: {
    type: Object,
    default: () => ({}),
  },
});
</script>
```

### Handle Errors During Form Submission

```vue
<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const form = reactive({
  email: '',
  password: '',
  errors: {},
});

const handleLogin = () => {
  form.errors = {}; // Clear previous errors
  
  router.post('/login', form, {
    onError: (errors) => {
      // errors is an object of validation errors
      form.errors = errors;
    },
    onSuccess: () => {
      // Redirect handling done by Laravel
    },
  });
};
</script>
```

## Advanced Examples

### Creating a Reusable Form Component

```vue
<!-- components/FormInput.vue -->
<template>
  <div class="form-group">
    <label :for="name" v-if="label">{{ label }}</label>
    <input
      :id="name"
      :value="modelValue"
      :type="type"
      :placeholder="placeholder"
      @input="$emit('update:modelValue', $event.target.value)"
      :class="{ 'is-invalid': error }"
    />
    <span v-if="error" class="error">{{ error }}</span>
  </div>
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  name: String,
  label: String,
  type: { type: String, default: 'text' },
  placeholder: String,
  error: String,
});

defineEmits(['update:modelValue']);
</script>
```

### Using the Component

```vue
<template>
  <form @submit.prevent="handleLogin">
    <FormInput
      v-model="form.email"
      name="email"
      label="Email"
      type="email"
      :error="errors.email"
    />
    <FormInput
      v-model="form.password"
      name="password"
      label="Password"
      type="password"
      :error="errors.password"
    />
    <button type="submit">Login</button>
  </form>
</template>

<script setup>
import FormInput from '@/components/FormInput.vue';
// ... rest of logic
</script>
```

---

These examples cover the most common Inertia.js patterns. Adapt them to your specific needs!
