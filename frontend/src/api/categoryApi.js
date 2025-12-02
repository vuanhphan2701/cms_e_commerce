import axios from "axios";

const BASE_URL = import.meta.env.VITE_BASE_URL;

// Lấy tất cả category (phân trang hoặc không tuỳ bạn)
export async function getCategories() {
  const res = await axios.get(`${BASE_URL}/category`);
  return res.data.data; // tuỳ JSON backend của bạn
}

// Lấy chi tiết 1 category
export async function getCategoryById(id) {
  const res = await axios.get(`${BASE_URL}/category/${id}`);
  return res.data.data;
}

// Tạo category mới
export async function createCategory(data) {
  const res = await axios.post(`${BASE_URL}/category`, data);
  return res.data;
}

// Cập nhật category
export async function updateCategory(id, data) {
  const res = await axios.put(`${BASE_URL}/category/${id}`, data);
  return res.data;
}

// Xoá category
export async function deleteCategory(id) {
  const res = await axios.delete(`${BASE_URL}/category/${id}`);
  return res.data;
}
