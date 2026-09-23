import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// ==========================================
// ALPINE START
// ==========================================
// NOTE: Do NOT register Alpine.store('app') here.
// The server-side app.blade.php <head> script already does it
// using the actual DB value (is_sitter). Registering it here
// would OVERRIDE the server value with localStorage.
// ==========================================

Alpine.start();