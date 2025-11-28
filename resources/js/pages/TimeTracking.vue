<template>
    <div class="flex justify-center max-w-xl mx-auto p-6 bg-gray-900 text-white rounded-lg shadow-lg mt-6">
        <form @submit.prevent="savetime">
          <h2 class="text-2xl font-bold mb-4 text-center">Track your Time</h2>
          <div class="space-x-4 space-y-2">
            <label class="font-semibold">Select Setting</label>
            <select v-model="selectedJobName" class="p-2 bg-gray-800 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-500">
              <option value="">-- Select Setting --</option>
              <option v-for="setting in jobNames" :key="setting.id" :value="setting.job_name">
                {{ setting.job_name }}
              </option>
            </select>
          </div>
            <div class="space-x-4 space-y-2">
                <label class="font-semibold">Start </label>
                <input v-model="time.start" @change="syncEndTime" type="datetime-local" class="p-2 bg-gray-800 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"/>
            </div>
            <div class="space-x-4 space-y-2">
                <label class="font-semibold">End </label>
                <input v-model="time.end" type="datetime-local" class="p-2 bg-gray-800 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"/>
            </div>
            <div class="space-x-4 space-y-2">
            <label class="font-semibold">Payroll</label>
            <select v-model="time.payroll" class="p-2 bg-gray-800 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-500">
              <option value="">-- New Payroll--</option>
              <option value="custom">-- Custom--</option>
              <option v-for="payrollLabel in payroll" :key="payrollLabel" :value="payrollLabel.payroll">
                {{ payrollLabel.payroll }}
              </option>
            </select>
            <input v-if="time.payroll === 'custom'" v-model="customPayroll" type="text" placeholder="Enter custom payroll" class="p-2 bg-gray-800 border border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"/>
            <span class="relative group cursor-pointer text-lg">
            ℹ️
            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-64 p-2 bg-gray-800 text-white text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300 z-10">
              You have three options:<br>
              New Payroll → It will automatically create a payroll with the format Month-Year-Selected Setting.<br>
              Custom → You can enter your own payroll identifier.<br>
              Select an existing Payroll.
            </div>
            </span>
          </div>
          <div class="flex items-center space-x-2">
            <label class="font-semibold text-gray-300">
              <input type="checkbox" v-model="time.isVacation" class="w-5 h-5 accent-gray-600 bg-gray-800 border border-gray-600 rounded focus:ring-2 focus:ring-gray-500"/>
              Mark as vacation
            </label>
            <span class="relative group cursor-pointer text-lg">
            ℹ️
            <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-64 p-2 bg-gray-800 text-white text-sm rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-300 z-10">
              Check this to mark the time as vacation.
              To do it right, use this example:
              If your vacation is from the 1st to the 7th, set the start to the 1st at 00:00 and the end to the 8th at 00:00.
            </div>
            </span>
          </div>
          <div class="flex space-x-4 mt-4">
            <button type="submit" class="w-full bg-blue-900 text-white font-semibold py-2 rounded-lg transition">{{time.id ? "Update" : "Save"}}</button>
            <button v-if="time.id" @click="resetForm" class="w-full bg-green-900 text-white font-semibold py-2 rounded-lg transition">New</button>
          </div>
        </form>
    </div>
    <div class="block justify-center max-w-xl mx-auto p-6 bg-gray-900 text-white rounded-lg shadow-lg">
      <div class="flex space-x-4 space-y-2">
      <h3 class="text-2xl font-bold mb-4 text-center">Registered Work Hours</h3>
  <label for="payrollFilter" class="block items-center space-x-2">Filter by Setting:</label>
  <select v-model="jobFilter" id="payrollFilter" class="font-semibold text-gray-800 h-10">
    <option value="">All</option>
    <option v-for="job in filteredJobName" :key="job" :value="job">
      {{job}}
    </option>
  </select>
  <label for="payrollFilter" class="block items-center space-x-2">Filter by Payroll:</label>
  <select v-model="payrollFilter" id="payrollFilter" class="font-semibold text-gray-800 h-10">
    <option value="">All</option>
    <option v-for="payrollLabel in filteredPayrolls" :key="payrollLabel.payroll" :value="payrollLabel.payroll">
      {{ payrollLabel.payroll }}
    </option>
  </select>
    </div>
    <Pagination :currentPage="currentPage" :totalPages="totalPages" @prev="currentPage--" @next="currentPage++"/>
    <p v-if="filteredWorkHours.length === 0" class="text-white text-lg text-center mt-2">There are no work hours to display</p>
      <ul>
        <li v-for="entry in paginatedWorkHours" :key="entry.id" class="flex items-center space-x-4 py-2">
          <span>{{ this.dayjs(entry.hour_start).format('DD/MM/YYYY HH:mm') }} - {{ this.dayjs(entry.hour_end).format('DD/MM/YYYY HH:mm') }} - {{ entry.payroll }}</span>
          <span v-if="entry.is_vacation" class="text-gray-500">(Vacation)</span>
          <button @click="editRegister(entry)"  class="bg-blue-900 text-white font-semibold py-1 px-2 text-sm rounded-lg transition">Edit</button>
          <button @click="deleteRegister(entry)"  class="bg-red-900 text-white font-semibold py-1 px-2 text-sm rounded-lg transition">Delete</button>
        </li>
      </ul>
    <Pagination :currentPage="currentPage" :totalPages="totalPages" @prev="currentPage--" @next="currentPage++"/>
    </div>
</template>


<script>
import axios from 'axios';
import { loadJobNames } from '@/composables/useJobs';
import { loadPayroll } from '@/composables/usePayroll';
import eventBus from "@/composables/eventBus";
import dayjs from 'dayjs';
import Pagination from '../composables/Pagination.vue';

export default{
    name: 'TimeTracking',
    emits: ['changeComponent'],
    data() {
        return{
            time: {
                jobname: "",
                start: "",
                end: "",
                payroll: "",
                isVacation: false,
        },
        jobNames: [],
        selectedJobName: "",
        workHours: [],
        payroll: [],
        dayjs: dayjs,
        customPayroll: "",
        payrollFilter:"",
        jobFilter:"",
        currentPage: 1,
        itemsPerPage:10,
    };
  },
  computed: {
    
    filteredJobName(){
    return [...new Set(this.payroll.map(p=> p.job_name))]; //Muestra las Settings disponibles de manera única.
    },

    filteredPayrolls() { //Devuelve los payroll que coincian con la setting seleccionada o todos si no se ha elegido ninguno.
    return this.payroll.filter(payrollLabel => {
      return this.jobFilter === "" || payrollLabel.job_name === this.jobFilter;
    });
    },
     
    filteredWorkHours() { //Filtra por el job_name y/o Setting seleccionada para mostrar los registros de horas 
      
    this.currentPage = 1;

    return this.workHours.filter(entry => {

    const matchesPayroll = this.payrollFilter === "" || entry.payroll === this.payrollFilter;
    const matchesJobName = this.jobFilter === "" || (entry.setting && entry.setting.job_name === this.jobFilter); //entry.setting evita un error si es null.
    
    return matchesPayroll && matchesJobName; //Muestra las entradas de ambos filtros.
    });
    },
    paginatedWorkHours() { //Calcula la paginación de la lista de horas.
    const start = (this.currentPage - 1) * this.itemsPerPage;
    const end = start + this.itemsPerPage;
    return this.filteredWorkHours.slice(start, end);
    },
    totalPages() {
    return Math.ceil(this.filteredWorkHours.length / this.itemsPerPage);
    }

},
  async mounted() {
        this.jobNames = await loadJobNames();
        this.payroll = await loadPayroll();
        this.loadWorkHours();
        if (!this.jobNames.length) {
          alert('"No saved settings found. Please create a setting before continuing.');
          this.$emit('changeComponent', 'settings');
        }
    },
    components: {
      Pagination //Son los botones de la lista.
    },

  methods: {

    loadWorkHours() { //Carga las horas
    axios
    .get('api/loadworkhours', {
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
        },
    })
    .then(response => {
      console.log('API Response:', response.data); 
      this.workHours = response.data.sort((a, b) => {
        return dayjs(a.hour_start).isBefore(dayjs(b.hour_start)) ? 1 : -1; //Ordena de más nuevo a más antiguo
      });
    })
    .catch(error => {
        console.error('Error loading Time', error);
    });
    },

    savetime(){ //Guarda o actualiza las horas
      
      if (this.time.start >= this.time.end){
        alert("Start time cannot be later than end time");
        return;
      }
      const startTime = new Date(this.time.start);
      const endTime = new Date(this.time.end);
      const difHours = (endTime - startTime) / (1000*60*60);
      if (!this.time.isVacation && difHours > 9){
        const confirmation = confirm(
          "The difference between start and end is more than 9 hours. Are you sure you want to continue?"
        );
        if (!confirmation) return;
      }
      if(!this.selectedJobName){
        alert("Select a Setting!");
        return;
      }
      if (this.time.payroll === 'custom'){
        this.time.payroll=this.customPayroll;
      }

      this.time.jobname = this.selectedJobName;
      
      console.log('payroll value:', this.time.payroll);

     console.log('Data being sent:', this.time);
     if (!this.time.id){ //Aquí crea un nuevo registro
     axios
     .post('api/trackingsave', this.time, {
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
            'Content-Type': 'application/json',
          },
     })
     .then(async(response) => {
          if (response.data.message === 'Time saved successfully') {
            alert('Time saved successfully!');
            this.loadWorkHours();
            this.payroll = await loadPayroll();
            this.resetForm();
            eventBus.emit("TimeSave");
          }
        })
        .catch((error) => {
          console.error('Error saving time', error);
          alert('Error saving time');
          if (error.response) {
            console.log("Código de estado:", error.response.status);
            console.log("Mensaje del servidor:", error.response.data);
      }
        });
     }else{ //Aquí actualiza un registro ya creado
      axios
      .put(`api/trackingupdate/${this.time.id}`,{
        start: this.time.start,
        end: this.time.end,
        jobname: this.time.jobname,
        payroll: this.time.payroll,
        isVacation: this.time.isVacation,
      },
      {
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
            'Content-Type': 'application/json',
        },
      })
      .then(async(response) => {
          if (response.data.message === 'Time updated successfully') {
            alert('Time updated successfully!');
            this.loadWorkHours();
            this.resetForm();
            this.payroll = await loadPayroll();
            eventBus.emit("TimeSave");
          }
        })
      .catch((error) => {
        console.error('Error updating time', error);
        alert('Error updating time');

        if (error.response) {
          console.log("Código de estado:", error.response.status);
          console.log("Mensaje del servidor:", error.response.data);
          console.log("Errores:", error.response.data.errors);
        }
      }); 
     }
     },

     editRegister(entry){ //Muestra en el formulario los datos para su modificación

        this.time.start= entry.hour_start;
        this.time.end= entry.hour_end;
        this.time.id=entry.id;
        this.time.payroll= entry.payroll;
        this.selectedJobName= entry.setting.job_name;
        this.time.isVacation= Boolean(entry.is_vacation);
     },

     resetForm(){ //Resetea el formulario.
       this.time = {
        start: "",
        end: "",
        id:"",
        payroll:"",
        isVacation: false,
       }
       this.selectedJobName=""
     },
     deleteRegister(entry){ //Elimina un registro seleccionado
      console.log('Deleting TimeTracking for:', entry);
      if (this.workHours.length <= 1) {
      alert('You can not delete the only record. Please create another one before deleting it.');
      return;
      }
      axios
        .post("api/trackingdelete/", entry, {
          headers: {
              Authorization: 'Bearer ' + localStorage.getItem('token'),
          },
        })
        .then((response) => {
          if(response.data.message === 'Time deleted succesfully'){
            alert ('Time deleted succesfully!');
            this.loadWorkHours();
            eventBus.emit("TimeSave");
          }
        })
        .catch((error) => {
        console.error('Error deleting time', error);
        alert('Error deleting time');

        if (error.response) {
            console.log("Código de estado:", error.response.status);
            console.log("Mensaje del servidor:", error.response.data);
      }
      });
     },
     syncEndTime() { //Sincroniza el tiempo puesto en Start con el de End
      if(!this.time.end) {
        this.time.end =this.time.start;
      }
     }
  }
}
</script>