import api from "./axios";

export const adminApi = {
  login: async (credentials) => {
    const response = await api.post('/admin/login', credentials, {
      withCredentials: true
    });
    return response.data;
  },
  refreshToken: async () => {
    const response = await api.post('/admin/refresh-token', {}, {
      withCredentials: true
    });
    return response.data;
  },
  logout: async () => {
    const response = await api.post('/admin/logout', {}, {
      withCredentials: true
    });
    return response.data;
  },
  
  // User Management
  getUsers: async (params) => {
    const response = await api.get('/admin/users', { params });
    return response.data;
  },
  getUser: async (id) => {
    const response = await api.get(`/admin/users/${id}`);
    return response.data;
  },
  updateUserStatus: async (id, data) => {
    const response = await api.post(`/admin/users/${id}/status`, data);
    return response.data;
  },
  verifyUserKYC: async (id, data) => {
    const response = await api.post(`/admin/users/${id}/verify-kyc`, data);
    return response.data;
  },
  resetUserPassword: async (id) => {
    const response = await api.post(`/admin/users/${id}/reset-password`);
    return response.data;
  },
  getUserActivity: async (id) => {
    const response = await api.get(`/admin/users/${id}/activity`);
    return response.data;
  },
};
