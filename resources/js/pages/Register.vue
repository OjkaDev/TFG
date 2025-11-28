<template>
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-r from-gray-200 to-gray-100">
    <div class="bg-white p-10 rounded-2xl shadow-xl max-w-md w-full">
      <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Create Your Account</h1>
      
      <form @submit.prevent="register" class="space-y-6">
        <div>
          <label for="name" class="block text-gray-700 font-medium mb-1">Name</label>
          <input v-model="name" type="text" id="name" placeholder="Enter your name"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
          <input v-model="email" type="email" id="email" placeholder="Enter your email"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
          <input v-model="password" type="password" id="password" placeholder="Enter your password"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
          <p v-if="passwordnotvalidate" class="text-red-500 mt-2 text-sm">{{ passwordnotvalidate }}</p>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium shadow-md hover:bg-blue-700 transition-transform transform hover:scale-105">
          Register
        </button>
      </form>

      <p class="text-center text-gray-600 mt-6">
        Already have an account? 
        <router-link to="/login" class="text-blue-600 font-semibold hover:underline">Log in</router-link>
      </p>
    </div>
  </div>
</template>


<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordnotvalidate:''
    };
  },
  methods: {

    validate(password){ //Valida la contraseña
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/;
      return regex.test(password);
      },

    register() { //Registra al usuario
      if (!this.name || !this.email || !this.password) {
        alert('All fields are required');
        return;
      }

      if(!this.validate(this.password)){
        this.passwordnotvalidate ='The password must have at least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character. '
        return;
      }else{
        this.passwordError= '';
      }
      axios
      .post('api/register', {
        name: this.name,
        email: this.email,
        password: this.password,
      })
      .then((response) => {
          console.log(response);
          localStorage.setItem('token', response.data.token);
          this.$router.push({ name: 'Dashboard' });
      })  
      .catch((error) => {
          console.error('Error de registro', error.response?.data||error.message);
          const errors = error.response.data.errors;
        // Convertimos los errores en una cadena legible
          const messages = Object.values(errors).flat().join('\n');
          alert(messages);
      });
    }
  }
};
</script>

 
  