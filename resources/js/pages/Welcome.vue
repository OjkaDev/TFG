<template>
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-r from-gray-200 to-gray-100">
        <div class="text-center bg-white p-10 rounded-2xl shadow-xl max-w-md w-full">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-6">Welcome!</h1>
            <p class="text-lg text-gray-600 mb-8">Start managing your work hours efficiently.</p>
            <div class="flex justify-between">
                <router-link to="/register" class="flex-1 mx-2 px-6 py-3 bg-blue-600 text-white rounded-lg font-medium shadow-md 
                    hover:bg-blue-700 transition-transform transform hover:scale-105 text-center">
                    Sign Up
                </router-link>
                <router-link to="/login" class="flex-1 mx-2 px-6 py-3 border border-blue-600 text-blue-600 rounded-lg font-medium shadow-md 
                    hover:bg-blue-600 hover:text-white transition-transform transform hover:scale-105 text-center">
                    Log In
                </router-link>
            </div>
        </div>
    </div>
</template>



<script>

export default {};
</script>
