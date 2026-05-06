import api from "./axios";

// Đăng nhập
export const login = async (credentials) => {
  const res = await api.post("auth/login", credentials);
  return res.data; // Tùy thuộc backend, có thể là res.data hoặc res.data.data
};

// Đăng ký
export const register = async (userData) => {
  const res = await api.post("auth/register", userData);
  return res.data;
};

// Đăng xuất
export const logout = async () => {
  const res = await api.post("auth/logout");
  return res.data;
};