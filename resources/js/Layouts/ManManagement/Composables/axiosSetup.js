import axios from "axios";
import { ref } from "vue";

const triggerSpinner = ref(false); // Global spinner state

// Add a request interceptor
axios.interceptors.request.use(
  (config) => {
    triggerSpinner.value = true; // Start spinner before request
    return config;
  },
  (error) => {
    triggerSpinner.value = false; // Stop spinner on error
    return Promise.reject(error);
  }
);

// Add a response interceptor
axios.interceptors.response.use(
  (response) => {
    triggerSpinner.value = false; // Stop spinner after response
    return response;
  },
  (error) => {
    triggerSpinner.value = false; // Stop spinner on response error
    return Promise.reject(error);
  }
);

export { triggerSpinner }; // Export to use in components
