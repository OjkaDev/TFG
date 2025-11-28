<template>
  <div class="bg-gray-600 min-h-screen">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-6">
        <div class="p-4 bg-gray-100 rounded shadow">
          <h2 class="text-lg font-semibold">Total Hours</h2>
          <p class="text-xl font-bold">{{ computedHours.totalHours }}h</p>
        </div>
        <div class="p-4 bg-gray-100 rounded shadow">
          <h2 class="text-lg font-semibold">Extra Hours</h2>
          <p class="text-xl font-bold">{{ computedHours.extraHours }}h</p>
        </div>
        <div class="p-4 bg-gray-100 rounded shadow">
          <h2 class="text-lg font-semibold">Gross Salary</h2>
          <p class="text-xl font-bold">${{ computedHours.grossSalary }}</p>
        </div>
        <div class="p-4 bg-gray-100 rounded shadow">
          <h2 class="text-lg font-semibold">Net Salary</h2>
          <p class="text-xl font-bold">${{ computedHours.netSalary }}</p>
        </div>
        <div class="p-4 bg-gray-100 rounded shadow">
          <h2 class="text-lg font-semibold">Night Hours</h2>
          <p class="text-xl font-bold">{{ computedHours.nightHours }}h</p>
        </div>
      </div>

    <div class="flex justify-center space-x-4 mt-4">
      <button @click="activeTab = 'home'; loadPayroll()" class="px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400 transition duration-200">Home</button>
      <button @click="activeTab = 'settings'" class="px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400 transition duration-200">Settings</button>
      <button @click="activeTab = 'timetracking'" class="px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400 transition duration-200">Time Tracking</button>
      <button @click="logout" class="px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400 transition duration-200">Logout</button>
    </div>

    <!-- Contenido dinámico de home -->
    <div  class="flex justify-center space-x-4 max-w-xl mx-auto p-6 bg-gray-900 text-white rounded-lg shadow-lg mt-6" v-if="activeTab === 'home'">
      <p>Welcome, {{ user?.name }}</p>
      <p>Email: {{ user?.email }}</p>
    </div>
    <div class="flex justify-center max-w-xl mx-auto p-6 bg-gray-900 text-white rounded-lg shadow-lg" v-if="activeTab === 'home'">
      <div class="flex items-center space-x-4">
      <label for="jobFilter" class="text-lg font-semibold">Select Setting:</label>
      <select v-model="jobFilter" id="payrollFilter" class="font-semibold text-gray-800 h-10">
    <option value="">All</option>
    <option v-for="job in filteredJobName" :key="job" :value="job">
      {{ job }}
    </option>
  </select>
  <label for="payrollFilter" class="block items-center space-x-2">Select Payroll:</label>
  <select v-model="payrollFilter" id="payrollFilter" class="font-semibold text-gray-800 h-10">
    <option value="">All</option>
    <option v-for="payrollLabel in filteredPayrolls" :key="payrollLabel.payroll" :value="payrollLabel.payroll">
      {{ payrollLabel.payroll }}
    </option>
  </select>
      <button @click="selectPayroll" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
        Select Payroll
      </button>
    </div>
    </div>

    <Settings v-if="activeTab === 'settings'" />
    <TimeTracking v-if="activeTab === 'timetracking'" @changeComponent="changeTab" />
  </div>
</template>

<script>
import axios from 'axios';
import { loadPayroll } from '../composables/usePayroll';
import eventBus from "@/composables/eventBus";
import Settings from './Settings.vue';
import TimeTracking from './TimeTracking.vue';

export default {
  name: 'Dashboard',
  components: {
    Settings,
    TimeTracking,
  },
  data() {
    return {
      user: null,
      activeTab: "home",
      payroll: [],
      payrollFilter:"",
      jobFilter: "",

      selectedPayroll: {
        job_name: "",
        payroll: "",
      },
      computedHours: {
        totalHours: "",
        extraHours: "",
        grossSalary: "",
        netSalary: "",
        nightHours: "",
      }
    };
  },
  computed: {

filteredPayrolls() { //Filtra Payrolls por Jobnames
return this.payroll.filter(payrollLabel => {
  return this.jobFilter === "" || payrollLabel.job_name === this.jobFilter;
    });
  },
filteredJobName(){ //Evita JobNames repetidos
  return [...new Set(this.payroll.map(p=> p.job_name))];
}
},
  async mounted() {
      this.welcome();
      this.payroll = await loadPayroll();
      console.log('Payroll: ', this.payroll);
      eventBus.on("TimeSave", this.loadComputerHour);
  },
  methods: {
    logout() { //Cierre de sesión
      fetch('api/logout', {
        method: 'POST',
        headers: {
          'Authorization': 'Bearer ' + localStorage.getItem('token'),
        },
      })
        .then(() => {
          localStorage.removeItem('token');
          this.$router.push({ name: 'Login' });
        })
        .catch((error) => {
          console.error('Error logging out', error);
        });
    },
    welcome(){ //Toma información de tú nombre y email.
      fetch('api/user', {
      method: 'GET',
      headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token'),
      },
    })
      .then((response) => response.json())
      .then((data) => {
        this.user = data;
      })
      .catch((error) => {
        console.error('Error loading data', error);
      });
    },
    selectPayroll(){ //Función para elegir que Payroll quiere que se muestre en los recuadros

      this.selectedPayroll.job_name = this.jobFilter;
      this.selectedPayroll.payroll = this.payrollFilter;

      console.log("SelectedPayroll: ", this.selectedPayroll);
      axios
        .post('api/selectPayroll', this.selectedPayroll, {
            headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
            'Content-Type': 'application/json',
          }
        })
        .then((response) => {
          if (response.data.message === 'Payroll selected correctly.'){
            alert('Selected payroll succesfully');
          }
        })
        .catch((error) => {
          console.error('Error selecting payroll', error);
          alert('Error selecting payroll');

          if (error.response) {
            console.log("Código de estado:", error.response.status);
            console.log("Mensaje del servidor:", error.response.data);
      }
        });
      this.loadComputerHour();
    },
    loadComputerHour(){ // Carga los cálculos y muestra las variables en los recuadros de arriba
      console.log("loadComputerHour triggered");
      this.selectedPayroll.payroll = this.payrollFilter;

      axios
        .get(`api/computedHours`, {
          params: {payroll: this.selectedPayroll.payroll},
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
          },
        })
        .then((response) => {
          console.log('API Response:', response.data);
          if (response.data) {
          this.computedHours = {
            totalHours: response.data.totalHours || "",
            extraHours: response.data.extraHours || "",
            grossSalary: response.data.grossSalary || "",
            netSalary: response.data.netSalary || "",
            nightHours: response.data.nightHours || "",
        };
        }
        })
        .catch((error) => {
          console.error('Error loading settings', error);
          if (error.response) {
            console.log("Código de estado:", error.response.status);
            console.log("Mensaje del servidor:", error.response.data);
      }
        });

      },
      changeTab(tabName) { //En caso que no se haya creado una Setting te redirige a ella.
        this.activeTab = tabName; 
    },
    async loadPayroll() { //Repetimos función para que se recargue los payroll en caso de haber creado uno nuevo.
    this.payroll = await loadPayroll();
    console.log("Payroll recargado:", this.payroll);
  }, 
    }
  }
</script>
