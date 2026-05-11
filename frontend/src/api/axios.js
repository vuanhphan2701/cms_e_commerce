import axios from "axios";

const api = axios.create({
  baseURL: 'http://ecommerce.test/cms/backend/public/api/',
});

// Request interceptor: attach token
api.interceptors.request.use(
  (config) => {
    // Check if the request is for admin or user
    const isAdminRequest = config.url?.includes('/admin/');
    const tokenKey = isAdminRequest ? "adminToken" : "token";
    const token = localStorage.getItem(tokenKey);
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor: handle token refresh
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    // Determine if it's an admin request
    const isAdminRequest = originalRequest.url?.includes('/admin/');
    const tokenKey = isAdminRequest ? "adminToken" : "token";
    const loginPath = isAdminRequest ? "/admin/login" : "/login";
    const refreshPath = isAdminRequest ? "admin/refresh-token" : "auth/refresh";

    // Skip token refresh for auth endpoints
    const isAuthEndpoint = originalRequest.url?.match(
      /(auth|admin)\/(login|register|forgot-password|reset-password|refresh-token)/
    );

    // If 401, not already retrying, and NOT an auth endpoint
    if (
      error.response?.status === 401 &&
      !originalRequest._retry &&
      !isAuthEndpoint
    ) {
      originalRequest._retry = true;

      try {
        const res = await axios.post(
          `http://ecommerce.test/cms/backend/public/api/${refreshPath}`,
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem(tokenKey)}`
            },
            withCredentials: true // Important for cookies
          }
        );

        const newToken = res.data.data.access_token;
        localStorage.setItem(tokenKey, newToken);

        originalRequest.headers.Authorization = `Bearer ${newToken}`;
        return api(originalRequest);
      } catch (refreshError) {
        // If refresh fails, logout and redirect to correct login page
        localStorage.removeItem(tokenKey);
        
        // Only redirect if not already on login page
        if (!window.location.pathname.includes(loginPath)) {
          window.location.href = loginPath;
        }
        return Promise.reject(refreshError);
      }
    }

    return Promise.reject(error);
  }
);

export default api;
