import axios from "axios";

const api = axios.create({
  baseURL: 'http://ecommerce.test/cms/backend/public/api/',
});

// Thêm interceptor để tự động đính kèm token vào request header nếu đã đăng nhập
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default api;
