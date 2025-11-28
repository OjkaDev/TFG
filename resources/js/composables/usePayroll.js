import axios from 'axios';

export async function loadPayroll() {  //función reutilizable para importar los Payroll registrados
    try {
        const response = await axios.get('api/loadpayroll', {
            headers: {
                Authorization: 'Bearer ' + localStorage.getItem('token'),
            },
        });
        return response.data;
    } catch (error) {
        console.error('Error loading Payroll', error);
        return []; 
    }
}
