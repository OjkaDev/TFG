<template>
    <div class="flex justify-center max-w-lg mx-auto p-6 bg-gray-900 text-white rounded-lg shadow-lg mt-6">
        <form @submit.prevent="saveSettings" class="space-y-4">
          <h2 class="text-2xl font-bold mb-4 text-center">Settings</h2>
          <div class="space-x-4">
            <label class="font-semibold">Select Setting:</label>
            <select v-model="selectedJobName" @change="loadSettings" class="p-2 bg-gray-800 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-500">
              <option value="">-- New Setting--</option>
              <option v-for="setting in jobNames" :key="setting.id" :value="setting.job_name">
                {{ setting.job_name }}
              </option>
            </select>
            <span class="relative group cursor-pointer text-lg">
            ℹ️
            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-64 p-2 bg-gray-800 text-white text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300 z-10">
              Select a saved setting to edit.
            </div>
            </span>
          </div>
          <div v-if="!selectedJobName" class="space-x-4">
            <label class="font-semibold">Setting Title:</label>
            <input v-model="settings.jobname" type="text" required class="p-2 bg-gray-800 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"/>
            <span class="relative group cursor-pointer text-lg">
            ℹ️
            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-64 p-2 bg-gray-800 text-white text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300 z-10">
              Enter the name you want to use for this setting.
            </div>
            </span>
          </div>
          <div class="space-x-4">
            <label class="font-semibold">Salary per hour:</label>
            <input v-model="settings.salaryperhour" type="number" required step="any" min="1" class="p-2 bg-gray-800 text-white border border-gray-700 rounded"/>
          </div>
          <div class="space-x-4">
            <label class="font-semibold">Total contract hour:</label>
            <input v-model="settings.totalcontracthour" type="number" required step="any" min="1" max="160" class="p-2 bg-gray-800 text-white border border-gray-700 rounded"/>
          </div>
          <div class="space-x-4">
            <label class="font-semibold">Night hours Start:</label>
            <input v-model="settings.nightstart" type="time" class="p-2 bg-gray-800 text-white border border-gray-700 rounded"/>
          </div>
          <div class="space-x-4">
            <label class="font-semibold">Night hours End:</label>
            <input v-model="settings.nightend" type="time" class="p-2 bg-gray-800 text-white border border-gray-700 rounded"/>
          </div>
          <div class="space-x-4">
            <label class="font-semibold">Nocturnal Bonus:</label>
            <input v-model="settings.nightsalary" type="number" step="any"class="p-2 bg-gray-800 text-white border border-gray-700 rounded"/>
            <span class="relative group cursor-pointer text-lg">
            ℹ️
            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-64 p-2 bg-gray-800 text-white text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300 z-10">
              Add only the extra value for night work, not the full hourly rate.
            </div>
            </span>
          </div>
          <div class="space-x-4">
          <label class="font-semibold">Extra Salary:</label>
            <input v-model="settings.extrasalary" type="number" step="any" class="p-2 bg-gray-800 text-white border border-gray-700 rounded"/>
            <span class="relative group cursor-pointer text-lg">
            ℹ️
            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-64 p-2 bg-gray-800 text-white text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300 z-10">
              Add any monthly bonuses or extra payments you receive.
            </div>
            </span>
          </div>
          <div class="space-x-4">
          <label class="font-semibold">Overtime Hourly Rate:</label>
            <input v-model="settings.extrahours" type="number" step="any" class="p-2 bg-gray-800 text-white border border-gray-700 rounded"/>
            <span class="relative group cursor-pointer text-lg">
            ℹ️
            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-64 p-2 bg-gray-800 text-white text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300 z-10">
              Enter the full rate for your overtime hour.
            </div>
            </span>
          </div>
          <div class="space-x-4">
            <label class="font-semibold">Tax percentage:</label>
            <input v-model="settings.taxpercentage" type="number" required step="any" min="1" max="100" class="p-2 bg-gray-800 text-white border border-gray-700 rounded"/>
          </div>
          <div class="flex space-x-4 mt-4">
          <button type="submit" class="w-full bg-blue-900 text-white font-semibold py-2 rounded-lg transition">
            {{ selectedJobName ? "Update" : "Save" }}
          </button>
          <button @click.prevent="deleteSetting(selectedJobName)" class=" w-full bg-red-900 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition">
          Delete Setting
        </button>
        </div>
        </form>
      </div>
  </template>
  
  
  <script>
  import axios from 'axios';
  import { loadJobNames } from '@/composables/useJobs'; 
  export default {
    name: 'Settings',
    data() {
        return {
            settings: {
                jobname:"",
                salaryperhour:"",
                totalcontracthour:"",
                nightstart:"",
                nightend:"",
                nightsalary:"",
                extrasalary:"",
                extrahours:"",
                taxpercentage:"",            
            },
            selectedJobName: "",
            jobNames: [],
            errors: {}
        };
    },
   async mounted() {
    this.jobNames= await loadJobNames();
  },

  methods: {

    loadSettings() { //Carga las settings ya guardadades
      if(!this.selectedJobName) {
        this.settings = {
        jobname: "",
        salaryperhour: "",
        totalcontracthour: "",
        nightstart: "",
        nightend: "",
        nightsalary: "",
        extrasalary: "",
        extrahours: "",
        taxpercentage: "",
      };
      return;
    }
      axios
        .get(`api/settings/${this.selectedJobName}`, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
          },
        })
        .then((response) => {
          console.log('API Response:', response.data);
          if (response.data) {
          this.settings = {
          jobname: response.data.job_name || "",
          salaryperhour: response.data.salary_per_hour || "",
          totalcontracthour: response.data.contract_hour || "",
          nightstart: this.formatTime(response.data.night_hours_start) || "",
          nightend: this.formatTime(response.data.night_hours_end) || "",
          nightsalary: response.data.night_salary || "",
          extrasalary: response.data.extra_salary || "",
          extrahours: response.data.salary_extra_hours|"",
          taxpercentage: response.data.tax_percentage || "",
        };
      }
        })
        .catch((error) => {
          console.error('Error loading settings', error);
        });
    },

    formatTime(time){  //Da formato al tiempo, ya que en la base de datos se guarda con segundos y daba error al traer los datos
      if(!time) return "";
      return time.slice(0,5);
    },


    saveSettings() { //Guarda las Settings

      Object.keys(this.settings).forEach((key) => {  //Evita problemas con la base de datos con cadenas vacías. Si está vacía lo reemplaza con Null.
      if (this.settings[key] === "") {
      this.settings[key] = null;
    }
  });

      console.log('Data being sent:', this.settings);

      if (!this.selectedJobName) {
      axios
        .post('api/settings', this.settings, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
            'Content-Type': 'application/json',
          },
        })
        .then(async(response) => {
          if (response.data.message === 'Settings saved successfully') {
            alert('Settings saved successfully!');
            this.jobNames= await loadJobNames();
          }
        })
        .catch((error) => {
          console.error('Error saving settings', error);
          alert('Error saving settings');
          if (error.response) {
            console.log("Código de estado:", error.response.status);
            console.log("Mensaje del servidor:", error.response.data);
      }
        });
      }else{
        axios
        .put(`api/settings/${this.selectedJobName}`, this.settings, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
            'Content-Type': 'application/json',
          },
        })
        .then((response) => {
          if (response.data.message === 'Settings saved successfully') {
            alert('Settings saved successfully!');
          }
        })
        .catch((error) => {
          console.error('Error updating settings', error);
          alert('Error updating settings');

          if (error.response) {
            console.log("Código de estado:", error.response.status);
            console.log("Mensaje del servidor:", error.response.data);
      }
        });
      }
    },
    deleteSetting(jobName) { //Elimina las setting seleccionada

      console.log('Deleting setting for job:', jobName);
      if(!jobName){
        alert('Select a Setting!');
        return;
      }

      axios
        .delete(`api/settings/${jobName}`, {
            headers: {
              Authorization: 'Bearer ' + localStorage.getItem('token'),
          },
        })
        .then(async(response) => {
          if(response.data.message === 'Setting deleted succesfully'){
            alert ('Setting deleted suscessfully!');
            this.jobNames= await loadJobNames();
            this.selectedJobName = "";
            this.loadSettings();
          }
        })
        .catch((error) => {
        console.error('Error deleting setting', error);
        alert('Error deleting setting');

        if (error.response) {
            console.log("Código de estado:", error.response.status);
            console.log("Mensaje del servidor:", error.response.data);
      }
      });
    }
  },
};
</script>