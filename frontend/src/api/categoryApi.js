// categoryApi.js
import api from "./axios";

export async function getAllCategories(options = {}) {
  const res = await api.get("category", { params: options });
  return {
    items: res.data.data.data,
    meta: res.data.data.meta || res.data.data,
  };
}

// Lấy tất cả categories
export async function getCategories() {
  const res = await api.get("category");
  return res.data.data; 
}

// Lấy chi tiết category
export async function getCategoryById(id) {
  const res = await api.get(`category/${id}`);
  return res.data.data;
}

// Tạo mới category
export async function createCategory(data) {
  const res = await api.post("category", data);
  return res.data.data;
}

// Cập nhật category
export async function updateCategory(id, data) {
  const res = await api.put(`category/${id}`, data);
  return res.data.data;
}

// Xoá category
export async function deleteCategory(id) {
  const res = await api.delete(`category/${id}`);
  return res.data.data;
}
