import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api', // Laravel's base API URL
});

axios.get('http://127.0.0.1:8000/api/tasks')
  .then(res => console.log(res.data))
  .catch(err => console.error(err));

export default api;
