<template>
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-r from-gray-200 to-gray-100">
    <div class="bg-white p-10 rounded-2xl shadow-xl max-w-md w-full">
      <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Welcome Back</h1>
      
      <form @submit.prevent="login" class="space-y-6">
        <div>
          <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
          <input v-model="email" type="email" id="email" placeholder="Enter your email" required
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
          <input v-model="password" type="password" id="password" placeholder="Enter your password" required
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-md hover:bg-blue-700 transition-transform transform hover:scale-105">
          Login
        </button>
      </form>

      <p class="text-center text-gray-600 mt-6">
        Don't have an account? 
        <router-link to="/register" class="text-blue-600 font-semibold hover:underline">Sign up</router-link>
      </p>
    </div>
  </div>
</template>


<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
    };
  },
  methods: {
    async login() {
      if (!this.email || !this.password) {
        alert('Todos los campos son obligatorios');
        return;
      }

      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password,
        });

        // Guarda el token en localStorage
        localStorage.setItem('token', response.data.token);

        // Redirige al usuario al Dashboard
        this.$router.push({ name: 'Dashboard' });

      } catch (error) {
        console.error('Error en login:', error.response?.data || error.message);
        alert('Credenciales incorrectas o error en el servidor');
      }
    }
  }
};
</script>
