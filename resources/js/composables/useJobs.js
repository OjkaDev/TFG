import axios from 'axios';

export async function loadJobNames() { //Función reutilizable para importar los Jobnames registrados.
    try {
        const response = await axios.get('api/settings/job-names', {
            headers: {
                Authorization: 'Bearer ' + localStorage.getItem('token'),
            },
        });
        return response.data;
    } catch (error) {
        console.error('Error loading job names', error);
        return []; 
    }
}
