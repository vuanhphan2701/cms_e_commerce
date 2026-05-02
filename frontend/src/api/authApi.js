import api from "./axios";

// Đăng nhập
export const login = async (credentials) => {
  const res = await api.post("auth/login", credentials);
  return res.data;
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

// Lấy thông tin user hiện tại
export const getMe = async () => {
  const res = await api.get("auth/me");
  return res.data;
};

// Quên mật khẩu - gửi email reset link
export const forgotPassword = async (email) => {
  const res = await api.post("auth/forgot-password", { email });
  return res.data;
};

// Đặt lại mật khẩu với token
export const resetPassword = async (data) => {
  const res = await api.post("auth/reset-password", data);
  return res.data;
};

// Gửi lại email xác thực
export const resendVerification = async (email) => {
  const res = await api.post("auth/email/resend", { email });
  return res.data;
};

// Xác thực OTP
export const verifyOtp = async (data) => {
  const res = await api.post("auth/email/verify-otp", data);
  return res.data;
};